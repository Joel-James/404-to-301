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

use DuckDev\FourNotFour\Addons\Catalog;
use DuckDev\FourNotFour\Core;
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
				'items' => Catalog::instance()->items( false ),
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
				'items' => Catalog::instance()->items( true ),
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
			return $this->error( 'rest_no_freemius', __( 'Licensing is not configured.', '404-to-301' ), 400 );
		}

		$result = $freemius->activate_license( $id, $key );

		if ( is_wp_error( $result ) ) {
			return $this->error( $result->get_error_code(), $result->get_error_message(), 400 );
		}

		return $this->respond(
			array(
				'success' => (bool) $result,
				'addon'   => Catalog::instance()->find( $id ),
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
			return $this->error( 'rest_no_freemius', __( 'Licensing is not configured.', '404-to-301' ), 400 );
		}

		$result = $freemius->deactivate_license( $id );

		if ( is_wp_error( $result ) ) {
			return $this->error( $result->get_error_code(), $result->get_error_message(), 400 );
		}

		return $this->respond(
			array(
				'success' => (bool) $result,
				'addon'   => Catalog::instance()->find( $id ),
			)
		);
	}
}
