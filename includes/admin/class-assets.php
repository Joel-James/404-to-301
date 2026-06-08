<?php
/**
 * Admin assets enqueue.
 *
 * Each plugin page mounts its own React app, so we enqueue only the
 * bundle that matches the current screen. The build artefacts come
 * from `npm run build` and their dependency lists live next to them
 * in `*.asset.php`, read via {@see Assets\Utils\Assets::manifest()}.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Api\Endpoint;
use DuckDev\FourNotFour\Plugin;
use DuckDev\FourNotFour\Utils\Assets as AssetManifest;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Assets
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Admin
 */
class Assets extends Singleton {

	/**
	 * Map of admin screen id => entry handle.
	 *
	 * The keys come from `Plugin::screens()`; the values match the
	 * `assets/src/<name>.js` entry files emitted into `build/`.
	 *
	 * @since 4.0.0
	 * @var array<string, string>
	 */
	private const HANDLES = array(
		'toplevel_page_404-to-301-redirects' => 'redirects',
		'redirects_page_404-to-301-logs'     => 'logs',
		'redirects_page_404-to-301-settings' => 'settings',
		'redirects_page_404-to-301-addons'   => 'addons',
	);

	/**
	 * Register hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the React bundle for the current plugin admin page.
	 *
	 * @since 4.0.0
	 *
	 * @param string $hook Current admin screen hook.
	 *
	 * @return void
	 */
	public function enqueue( $hook ): void {
		if ( ! isset( self::HANDLES[ $hook ] ) ) {
			return;
		}

		$entry  = self::HANDLES[ $hook ];
		$handle = 'd404-' . $entry;
		$asset  = AssetManifest::manifest( $entry );
		$src    = D404_URL . 'build/' . $entry . '.js';

		wp_enqueue_script(
			$handle,
			$src,
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( $handle, '404-to-301', D404_DIR . 'languages' );

		wp_localize_script(
			$handle,
			'd404',
			$this->script_vars( $entry )
		);

		$css_path = D404_DIR . 'build/' . $entry . '.css';
		if ( is_readable( $css_path ) ) {
			wp_enqueue_style(
				$handle,
				D404_URL . 'build/' . $entry . '.css',
				array( 'wp-components' ),
				$asset['version']
			);
		}
	}

	/**
	 * Localized script vars passed into every plugin React app.
	 *
	 * Filterable so add-ons can inject their own payload (e.g. a
	 * license key or feature flags).
	 *
	 * @since 4.0.0
	 *
	 * @param string $entry Entry handle (logs / redirects / settings / addons).
	 *
	 * @return array
	 */
	private function script_vars( string $entry ): array {
		$pages    = Plugin::pages();
		$settings = \DuckDev\FourNotFour\Core::instance()->settings();

		$vars = array(
			'version'          => Plugin::version(),
			'slug'             => Plugin::SLUG,
			'name'             => Plugin::name(),
			'page'             => $entry,
			'pages'            => array_combine(
				array_keys( $pages ),
				array_map(
					static function ( $p ) {
						return $p['url'];
					},
					$pages
				)
			),
			'restUrl'          => rest_url( Endpoint::NAMESPACE . '/' ),
			'restNonce'        => wp_create_nonce( 'wp_rest' ),
			'adminUrl'         => admin_url(),

			/*
			 * Hint for the React layer: is the v3 → v4 migration
			 * still in play on this site? When false, the Logs page
			 * skips mounting the migration banner entirely and the
			 * `useMigration` hook never fires its initial `GET
			 * /migration` request — there's nothing to poll for once
			 * the legacy table has been drained.
			 *
			 * Reading the cheap `logs_migrated` option is far lighter
			 * than the alternative (every page load fetches the
			 * migration status from REST + queries the legacy table
			 * for a count).
			 */
			'migrationPending' => $settings ? ! (bool) $settings->get( 'logs_migrated', false ) : false,
		);

		/**
		 * Filter the localised script vars for an admin page.
		 *
		 * @since 4.0.0
		 *
		 * @param array  $vars  Localised vars.
		 * @param string $entry Entry handle.
		 */
		return (array) apply_filters( '404_to_301_admin_script_vars', $vars, $entry );
	}
}
