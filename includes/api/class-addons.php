<?php
/**
 * Addons REST endpoint.
 *
 * Surfaces the addon catalog and per-addon license operations to the
 * React Addons page. All data comes from the Freemius SDK — there is
 * no fallback / stub list, so the page renders empty until the
 * project is correctly configured on Freemius and the SDK can talk
 * to the API.
 *
 * Three routes:
 *
 *   GET    /addons                       — fetch the catalog.
 *   POST   /addons/refresh               — force the SDK cache to
 *                                          rebuild from the API.
 *   POST   /addons/{id}/license          — activate a license key
 *                                          against the addon's
 *                                          Freemius client.
 *   DELETE /addons/{id}/license          — deactivate the same.
 *
 * `{id}` is the addon's Freemius project id (an integer), matching
 * the `id` field returned in every catalog row.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Core;
use DuckDev\FourNotFour\Freemius;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Addons
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Api
 */
class Addons extends Endpoint {

	/**
	 * Register the routes with the REST API.
	 *
	 * Called from {@see Endpoint::__construct()} on `rest_api_init`.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/addons',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'list' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/addons/refresh',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'refresh' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/addons/(?P<id>\d+)/license',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'activate_license' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => array(
						'key' => array(
							'type'     => 'string',
							'required' => true,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'deactivate_license' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);
	}

	/**
	 * GET /addons — return the decorated catalog.
	 *
	 * Uses the SDK's 24h cache. The first request after activation
	 * (or after the cache expires) does a live API call; subsequent
	 * requests are local.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response Response with shape `{ items: [...] }`.
	 */
	public function list( WP_REST_Request $request ): WP_REST_Response {
		unset( $request );

		return $this->respond(
			array(
				'items' => $this->shape_catalog( false ),
			)
		);
	}

	/**
	 * POST /addons/refresh — bypass the SDK cache and re-fetch.
	 *
	 * Used by the "Refresh" button in the Addons page header. The
	 * SDK rate-limits its own remote requests to one per five
	 * minutes — repeated clicks return the last-known catalog
	 * unchanged.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response Response with the freshly-pulled list.
	 */
	public function refresh( WP_REST_Request $request ): WP_REST_Response {
		unset( $request );

		return $this->respond(
			array(
				'items' => $this->shape_catalog( true ),
			)
		);
	}

	/**
	 * POST /addons/{id}/license — activate a license key.
	 *
	 * Delegates to {@see Freemius::activate_license()}. Wraps the
	 * SDK's WP_Error result with a 400 status so the React layer's
	 * `apiFetch` rejects cleanly.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request. Path captures
	 *                                 `id`; body provides `key`.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function activate_license( WP_REST_Request $request ) {
		$freemius = Core::instance()->freemius();
		$id       = (int) $request['id'];
		$key      = (string) $request->get_param( 'key' );

		if ( ! $freemius || ! $freemius->is_ready() ) {
			return new WP_Error(
				'rest_no_freemius',
				__( 'Licensing is not configured.', '404-to-301' ),
				array( 'status' => 400 )
			);
		}

		$result = $freemius->activate_license( $id, $key );

		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				$result->get_error_code(),
				$result->get_error_message(),
				array( 'status' => 400 )
			);
		}

		return $this->respond(
			array(
				'success' => (bool) $result,
				'addon'   => $this->shape_addon_by_id( $id ),
			)
		);
	}

	/**
	 * DELETE /addons/{id}/license — deactivate a license.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function deactivate_license( WP_REST_Request $request ) {
		$freemius = Core::instance()->freemius();
		$id       = (int) $request['id'];

		if ( ! $freemius || ! $freemius->is_ready() ) {
			return new WP_Error(
				'rest_no_freemius',
				__( 'Licensing is not configured.', '404-to-301' ),
				array( 'status' => 400 )
			);
		}

		$result = $freemius->deactivate_license( $id );

		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				$result->get_error_code(),
				$result->get_error_message(),
				array( 'status' => 400 )
			);
		}

		return $this->respond(
			array(
				'success' => (bool) $result,
				'addon'   => $this->shape_addon_by_id( $id ),
			)
		);
	}

	// --------------------------------------------------------------------- //
	// Internals.
	// --------------------------------------------------------------------- //

	/**
	 * Shape the SDK catalog rows for the React UI.
	 *
	 * The SDK returns its rows in a slightly noisy shape (lots of
	 * fields the UI doesn't need); this method extracts just what
	 * the React side consumes and decorates each row with:
	 *
	 *   - `is_active`         — whether the addon plugin has
	 *                           registered itself locally (i.e. it's
	 *                           installed and active).
	 *   - `is_license_active` — whether the stored license is
	 *                           currently activated on Freemius.
	 *   - `license_key`       — raw key value, so the input can
	 *                           pre-fill and go read-only when active.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $force Force a Freemius API refresh.
	 *
	 * @return array<int, array> Empty array when the SDK isn't
	 *                           configured or returns nothing.
	 */
	private function shape_catalog( bool $force ): array {
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

		foreach ( $this->get_wporg_addons() as $addon ) {
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
	 * Free addons hosted on the wordpress.org plugin repository.
	 *
	 * Returns rows in the same shape as Freemius addons so the React
	 * layer can render them in the same grid. Each entry only carries
	 * presentational data — there's no license, no remote update flow,
	 * and no Freemius registration. WordPress itself handles install
	 * and updates once the user clicks through.
	 *
	 * The list is keyed by wp.org slug. To add a real addon, append a
	 * row here (or use the `404_to_301_wporg_addons` filter from a
	 * site-specific plugin). Banner / icon URLs follow the predictable
	 * `ps.w.org` asset path so most entries need nothing more than a
	 * slug, title, and description.
	 *
	 * `id` is a negative integer derived from a CRC32 of the slug so
	 * it can't collide with a Freemius project id (those are positive).
	 * Stable across requests so React keys don't churn.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array> Shaped rows ready for the catalog.
	 */
	private function get_wporg_addons(): array {
		/**
		 * Filter the raw list of wp.org free addons before shaping.
		 *
		 * Each entry is an associative array keyed by wp.org slug with:
		 *   - title        (string, required)
		 *   - description  (string)
		 *   - banner       (string URL, optional — defaults to ps.w.org 772x250)
		 *   - banner_large (string URL, optional — defaults to ps.w.org 1544x500)
		 *   - icon         (string URL, optional — defaults to ps.w.org)
		 *   - homepage     (string URL, optional — defaults to wp.org page)
		 *
		 * @since 4.0.0
		 *
		 * @param array<string, array> $addons Slug-keyed addon definitions.
		 */
		$raw = (array) apply_filters(
			'404_to_301_wporg_addons',
			array(
				'404-to-301' => array( // @todo Update this slug to match the plugin slug.
					'title'       => __( 'Redirects Importer', '404-to-301' ),
					'description' => __( 'Bulk import 301 redirects into 404 to 301 from CSV files or migrate them in from other redirect plugins like Redirection, Rank Math, and Yoast — no manual re-entry.', '404-to-301' ),
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

			$items[] = array(
				'id'                => -abs( (int) sprintf( '%u', crc32( $slug ) ) % PHP_INT_MAX ),
				'source'            => 'wporg',
				'slug'              => $slug,
				'title'             => (string) $addon['title'],
				'icon'              => (string) ( $addon['icon'] ?? "https://ps.w.org/{$slug}/assets/icon-128x128.png" ),
				'link'              => "https://downloads.wordpress.org/plugin/{$slug}.latest-stable.zip",
				'description'       => (string) ( $addon['description'] ?? '' ),
				'homepage'          => (string) ( $addon['homepage'] ?? "https://wordpress.org/plugins/{$slug}/" ),
				'is_premium'        => false,
				'is_wporg'          => true,
				'is_active'         => $this->is_wporg_addon_active( $slug ),
				'is_license_active' => false,
				'license_key'       => '',
				'banner'            => (string) ( $addon['banner'] ?? "https://ps.w.org/{$slug}/assets/banner-772x250.png" ),
				'banner_large'      => (string) ( $addon['banner_large'] ?? "https://ps.w.org/{$slug}/assets/banner-1544x500.png" ),
			);
		}

		return $items;
	}

	/**
	 * Whether a wp.org addon plugin is installed and active locally.
	 *
	 * Scans `active_plugins` (and network-active on multisite) for
	 * any file path under the addon's slug directory, so we catch
	 * both `slug/slug.php` and `slug/anything.php` layouts without
	 * having to know the bootstrap filename up front.
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

	/**
	 * Return the single shaped row for a given id.
	 *
	 * Used inside the activate / deactivate handlers so the response
	 * carries the fresh row back to the React layer in one round-trip.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id Freemius project id.
	 *
	 * @return array Empty array when the id isn't in the catalog.
	 */
	private function shape_addon_by_id( int $id ): array {
		foreach ( $this->shape_catalog( false ) as $addon ) {
			if ( (int) $addon['id'] === $id ) {
				return $addon;
			}
		}

		return array();
	}
}
