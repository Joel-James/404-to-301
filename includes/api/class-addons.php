<?php
/**
 * Addons REST endpoint.
 *
 * Surfaces the addon catalogue for the React Addons page. When the
 * parent Freemius client is configured, the catalogue comes from
 * Freemius; otherwise we serve a built-in stub list so the UI still
 * has something to render.
 *
 * Each addon has its own Freemius project and therefore its own
 * license — activation/deactivation routes accept the addon's slug
 * (or Freemius id) and operate on the matching per-addon client.
 *
 * Routes:
 *   GET    /addons                       — list the catalogue.
 *   POST   /addons/{slug}/license        — activate a key for one addon.
 *   DELETE /addons/{slug}/license        — deactivate the key for one addon.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Core;
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
	 * Register the routes.
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
					'args'                => array(
						'force' => array( 'type' => 'boolean', 'default' => false ),
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/addons/(?P<slug>[a-z0-9_\-]+)/license',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'activate_license' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => array(
						'key' => array( 'type' => 'string', 'required' => true ),
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
	 * GET /addons — return the catalogue.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function list( WP_REST_Request $request ): WP_REST_Response {
		$catalog = $this->catalog( (bool) $request->get_param( 'force' ) );

		// Decorate every row with its license status before returning.
		$catalog = array_map( array( $this, 'with_license_status' ), $catalog );

		return $this->respond(
			array(
				'items' => array_values( $catalog ),
				'total' => count( $catalog ),
			)
		);
	}

	/**
	 * POST /addons/{slug}/license.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function activate_license( WP_REST_Request $request ) {
		$slug  = (string) $request['slug'];
		$key   = (string) $request->get_param( 'key' );
		$addon = $this->find_addon( $slug );

		if ( ! $addon ) {
			return new WP_Error( 'rest_not_found', __( 'Add-on not found.', '404-to-301' ), array( 'status' => 404 ) );
		}

		$client = $this->addon_client( $addon );

		if ( ! $client ) {
			return new WP_Error(
				'rest_no_freemius',
				__( 'This add-on does not have licensing configured.', '404-to-301' ),
				array( 'status' => 400 )
			);
		}

		$result = $client->license()->activate( $key );

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
				'addon'   => $this->with_license_status( $addon ),
			)
		);
	}

	/**
	 * DELETE /addons/{slug}/license.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function deactivate_license( WP_REST_Request $request ) {
		$slug  = (string) $request['slug'];
		$addon = $this->find_addon( $slug );

		if ( ! $addon ) {
			return new WP_Error( 'rest_not_found', __( 'Add-on not found.', '404-to-301' ), array( 'status' => 404 ) );
		}

		$client = $this->addon_client( $addon );

		if ( ! $client ) {
			return new WP_Error(
				'rest_no_freemius',
				__( 'This add-on does not have licensing configured.', '404-to-301' ),
				array( 'status' => 400 )
			);
		}

		$result = $client->license()->deactivate();

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
				'addon'   => $this->with_license_status( $addon ),
			)
		);
	}

	/**
	 * Fetch the addon catalogue, falling back to the stub list when
	 * Freemius isn't configured.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $force Force a remote refresh (Freemius-only).
	 *
	 * @return array
	 */
	private function catalog( bool $force ): array {
		$freemius = Core::instance()->freemius();

		if ( $freemius && $freemius->is_ready() ) {
			$service = $freemius->addon();
			$catalog = $service ? (array) $service->get_addons( $force ) : array();
		} else {
			$catalog = $this->fallback_catalog();
		}

		/**
		 * Filter the addons catalogue before licensing decoration.
		 *
		 * @since 4.0.0
		 *
		 * @param array $catalog Addons.
		 */
		return (array) apply_filters( '404_to_301_addons_catalog', $catalog );
	}

	/**
	 * Look up a single addon by slug.
	 *
	 * @since 4.0.0
	 *
	 * @param string $slug Addon slug.
	 *
	 * @return array|null
	 */
	private function find_addon( string $slug ): ?array {
		foreach ( $this->catalog( false ) as $addon ) {
			if ( ( $addon['slug'] ?? '' ) === $slug ) {
				return $addon;
			}
		}

		return null;
	}

	/**
	 * Get (or build) the Freemius client for a specific addon.
	 *
	 * @since 4.0.0
	 *
	 * @param array $addon Addon row.
	 *
	 * @return \DuckDev\Freemius\Freemius|null
	 */
	private function addon_client( array $addon ) {
		$freemius = Core::instance()->freemius();

		if ( ! $freemius ) {
			return null;
		}

		$id = (int) ( $addon['freemius_id'] ?? 0 );

		if ( $id <= 0 ) {
			return null;
		}

		return $freemius->for_addon(
			$id,
			array(
				'slug'       => (string) ( $addon['slug'] ?? '' ),
				'main_file'  => (string) ( $addon['main_file'] ?? '' ),
				'public_key' => (string) ( $addon['freemius_public_key'] ?? '' ),
				'is_premium' => (bool) ( $addon['is_premium'] ?? true ),
				'has_addons' => false,
			)
		);
	}

	/**
	 * Decorate an addon row with its current license status.
	 *
	 * Adds three keys:
	 *  - `has_license`    — whether the addon supports licensing at all.
	 *  - `license_status` — one of 'active' | 'inactive' | 'unknown'.
	 *  - `license_masked` — last four characters of the key, prefixed
	 *                      with `***-` (or empty string).
	 *
	 * @since 4.0.0
	 *
	 * @param array $addon Addon row.
	 *
	 * @return array
	 */
	private function with_license_status( array $addon ): array {
		$client = $this->addon_client( $addon );

		$addon['has_license']    = (bool) $client;
		$addon['license_status'] = 'unknown';
		$addon['license_masked'] = '';

		if ( ! $client ) {
			return $addon;
		}

		// The Freemius lib stores activation data under the addon's
		// option key. The license service exposes it via a protected
		// `get_activation_data()` — but the constants for ACTIVATED /
		// DEACTIVATED are on the `Service` parent. We probe the
		// public option key directly to keep this read-only and not
		// trigger any remote API calls.
		$option = get_option( 'duckdev_freemius_' . ( $addon['freemius_id'] ?? 0 ), array() );
		if ( ! is_array( $option ) ) {
			$option = array();
		}

		$status = (string) ( $option['status'] ?? '' );
		$key    = (string) ( $option['activation_params']['license_key'] ?? '' );

		if ( 'activated' === $status && '' !== $key ) {
			$addon['license_status'] = 'active';
			$addon['license_masked'] = '***-' . substr( $key, -4 );
		} elseif ( '' !== $status ) {
			$addon['license_status'] = 'inactive';
		}

		return $addon;
	}

	/**
	 * Stub catalogue used when the parent Freemius client isn't
	 * configured. Each entry mirrors the shape Freemius returns so
	 * the UI doesn't need to branch on the source.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	private function fallback_catalog(): array {
		return array(
			array(
				'id'                  => 1,
				'slug'                => 'lazy-load-for-comments',
				'title'               => __( 'Lazy Load for Comments', '404-to-301' ),
				'description'         => __( 'Defer the comments markup until the visitor scrolls or clicks — pairs well with 404 to 301 to keep page speed high.', '404-to-301' ),
				'icon'                => '',
				'tags'                => array( 'performance' ),
				'cta_url'             => 'https://wordpress.org/plugins/lazy-load-for-comments/',
				'installed'           => false,
				'active'              => false,
				'is_premium'          => false,
				'freemius_id'         => 0,
				'freemius_public_key' => '',
				'main_file'           => '',
			),
			array(
				'id'                  => 2,
				'slug'                => 'loggedin',
				'title'               => __( 'Loggedin', '404-to-301' ),
				'description'         => __( 'Limit the number of concurrent sessions a user can keep open. A nice companion when you start cleaning up sloppy 404 traffic.', '404-to-301' ),
				'icon'                => '',
				'tags'                => array( 'security' ),
				'cta_url'             => 'https://wordpress.org/plugins/loggedin/',
				'installed'           => false,
				'active'              => false,
				'is_premium'          => false,
				'freemius_id'         => 0,
				'freemius_public_key' => '',
				'main_file'           => '',
			),
			array(
				'id'                  => 3,
				'slug'                => '404-to-301-log-manager',
				'title'               => __( '404 to 301 — Log Manager (Pro)', '404-to-301' ),
				'description'         => __( 'Premium add-on: digest emails on a schedule, advanced filters, and bulk export of 404 logs.', '404-to-301' ),
				'icon'                => '',
				'tags'                => array( 'pro', 'reporting' ),
				'cta_url'             => 'https://duckdev.com/products/404-to-301/',
				'installed'           => false,
				'active'              => false,
				'is_premium'          => true,
				'freemius_id'         => 0,
				'freemius_public_key' => '',
				'main_file'           => '',
			),
		);
	}
}
