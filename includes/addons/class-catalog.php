<?php
/**
 * Addon catalog service.
 *
 * Owns everything about *assembling* the addon catalog the Addons page
 * renders: it pulls the Freemius project list, splices in the free
 * wp.org addons, decorates each row with local install / license state,
 * and exposes the filters self-hosted builds use to customise the list.
 *
 * This is the domain layer behind {@see \DuckDev\FourNotFour\Api\Addons}
 * — the REST controller stays a thin HTTP wrapper, mirroring how
 * {@see \DuckDev\FourNotFour\Api\Migration} delegates to
 * {@see \DuckDev\FourNotFour\Migration\Migrator}. Keeping it here (rather
 * than in the controller) means the same catalog can feed WP-CLI or unit
 * tests without going through a `WP_REST_Request`.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Addons;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Core;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Catalog
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Addons
 */
class Catalog extends Singleton {

	/**
	 * Build the decorated catalog for the React UI.
	 *
	 * The SDK returns its rows in a slightly noisy shape (lots of
	 * fields the UI doesn't need); this method extracts just what the
	 * React side consumes and decorates each row with:
	 *
	 *   - `is_active`         — whether the addon plugin has registered
	 *                           itself locally (i.e. it's installed and
	 *                           active).
	 *   - `is_license_active` — whether the stored license is currently
	 *                           activated on Freemius.
	 *   - `license_key`       — raw key value, so the input can pre-fill
	 *                           and go read-only when active.
	 *
	 * Uses the SDK's 24h cache unless `$force` is set.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $force Force a Freemius API refresh.
	 *
	 * @return array<int, array> Empty array when the SDK isn't
	 *                           configured or returns nothing.
	 */
	public function items( bool $force = false ): array {
		$freemius = Core::instance()->freemius();
		$catalog  = array();
		$items    = array();

		if ( $freemius && $freemius->is_ready() ) {
			$catalog    = $freemius->get_addons( $force );
			$registered = $freemius->get_registered_addons();
			$licenses   = $freemius->get_license_items();

			foreach ( (array) $catalog as $addon ) {
				$id      = (int) ( $addon['id'] ?? 0 );
				$license = $licenses[ $id ] ?? array();

				$items[] = array(
					'id'                => $id,
					'source'            => 'freemius',
					'title'             => (string) ( $addon['title'] ?? '' ),
					'icon'              => (string) ( $addon['icon'] ?? '' ),
					'link'              => (string) ( $addon['link'] ?? '' ),
					'description'       => (string) ( $addon['info']['description'] ?? '' ),
					'homepage'          => (string) ( $addon['info']['url'] ?? '' ),
					'is_premium'        => (bool) ( $addon['is_premium'] ?? false ),
					'is_wporg'          => false,
					'is_active'         => isset( $registered[ $id ] ),
					'is_license_active' => (bool) ( $license['active'] ?? false ),
					'license_key'       => (string) ( $license['key'] ?? '' ),
					'banner'            => (string) ( $addon['info']['card_banner_url'] ?? '' ),
					'banner_large'      => (string) ( $addon['info']['banner_url'] ?? '' ),
				);
			}
		}

		foreach ( $this->wporg_addons() as $addon ) {
			$items[] = $addon;
		}

		/**
		 * Filter the shaped addon catalog before it goes over REST.
		 *
		 * Useful for self-hosted / white-label builds that want to
		 * splice in their own rows without standing up a separate
		 * Freemius project.
		 *
		 * @since 4.0.0
		 *
		 * @param array $items   Shaped catalog rows.
		 * @param array $catalog Raw SDK catalog rows.
		 */
		return (array) apply_filters( '404_to_301_addons_catalog', $items, $catalog );
	}

	/**
	 * Return the single shaped row for a given id.
	 *
	 * Used by the activate / deactivate handlers so the response can
	 * carry the fresh row back to the React layer in one round-trip.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id Freemius project id.
	 *
	 * @return array Empty array when the id isn't in the catalog.
	 */
	public function find( int $id ): array {
		foreach ( $this->items( false ) as $addon ) {
			if ( (int) $addon['id'] === $id ) {
				return $addon;
			}
		}

		return array();
	}

	/**
	 * Free addons distributed outside the Freemius catalog.
	 *
	 * Returns rows in the same shape as Freemius addons so the React
	 * layer can render them in the same grid. Each entry only carries
	 * presentational data — there's no license and no Freemius
	 * registration.
	 *
	 * Until the wp.org plugin review team approves the addons, the
	 * rows below point at their GitHub repos (download = Releases
	 * page, icon/banner = `.wporg` folder in the repo). Once an addon
	 * lands on wp.org, swap its row back to the `ps.w.org` asset URLs
	 * and the wp.org install/update flow.
	 *
	 * `id` is a negative integer derived from a CRC32 of the slug so
	 * it can't collide with a Freemius project id (those are positive).
	 * Stable across requests so React keys don't churn.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array> Shaped rows ready for the catalog.
	 */
	private function wporg_addons(): array {
		/**
		 * Filter the raw list of free addons before shaping.
		 *
		 * Each entry is an associative array keyed by plugin slug with:
		 *   - title        (string, required)
		 *   - description  (string)
		 *   - banner       (string URL, optional)
		 *   - banner_large (string URL, optional)
		 *   - icon         (string URL, optional)
		 *   - homepage     (string URL, optional)
		 *   - link         (string URL, optional — download / releases page)
		 *
		 * @since 4.0.0
		 *
		 * @param array<string, array> $addons Slug-keyed addon definitions.
		 */
		$raw = (array) apply_filters(
			'404_to_301_wporg_addons',
			array(
				'404-to-301-logs-exporter'      => array(
					'title'       => __( 'Logs Exporter', '404-to-301' ),
					'description' => __( 'One-click CSV export for the 404 to 301 plugin\'s error log — filter-aware, streamed, and ready for Excel, Sheets or Numbers.', '404-to-301' ),
				),
				'404-to-301-redirects-importer' => array(
					'title'       => __( 'Redirects Importer', '404-to-301' ),
					'description' => __( 'Bulk import custom redirects into 404 to 301 from CSV files or migrate them in from other redirect plugins like Redirection by John Godley and 301 Redirects – Redirect Manager by WebFactory — no manual re-entry.', '404-to-301' ),
				),
			)
		);

		if ( empty( $raw ) ) {
			return array();
		}

		$items = array();

		foreach ( $raw as $slug => $addon ) {
			$slug = sanitize_key( (string) $slug );
			if ( '' === $slug || empty( $addon['title'] ) ) {
				continue;
			}

			$repo = "https://raw.githubusercontent.com/duckdev/{$slug}/main/.wporg";

			$items[] = array(
				'id'                => -abs( (int) sprintf( '%u', crc32( $slug ) ) % PHP_INT_MAX ),
				'source'            => 'wporg',
				'slug'              => $slug,
				'title'             => (string) $addon['title'],
				'icon'              => (string) ( $addon['icon'] ?? "{$repo}/icon.png" ),
				'link'              => (string) ( $addon['link'] ?? "https://github.com/duckdev/{$slug}/releases" ),
				'description'       => (string) ( $addon['description'] ?? '' ),
				'homepage'          => (string) ( $addon['homepage'] ?? "https://duckdev.com/addon/{$slug}/" ),
				'is_premium'        => false,
				'is_wporg'          => true,
				'is_active'         => $this->is_wporg_addon_active( $slug ),
				'is_license_active' => false,
				'license_key'       => '',
				'banner'            => (string) ( $addon['banner'] ?? "{$repo}/banner.png" ),
				'banner_large'      => (string) ( $addon['banner_large'] ?? "{$repo}/banner-large.png" ),
			);
		}

		return $items;
	}

	/**
	 * Whether a wp.org addon plugin is installed and active locally.
	 *
	 * Scans `active_plugins` (and network-active on multisite) for any
	 * file path under the addon's slug directory, so we catch both
	 * `slug/slug.php` and `slug/anything.php` layouts without having to
	 * know the bootstrap filename up front.
	 *
	 * @since 4.0.0
	 *
	 * @param string $slug wp.org plugin slug.
	 *
	 * @return bool
	 */
	private function is_wporg_addon_active( string $slug ): bool {
		$prefix = $slug . '/';
		$active = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active = array_merge( $active, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		foreach ( $active as $plugin ) {
			if ( is_string( $plugin ) && 0 === strpos( $plugin, $prefix ) ) {
				return true;
			}
		}

		return false;
	}
}
