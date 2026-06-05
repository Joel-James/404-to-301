<?php
/**
 * Base class for REST API endpoints.
 *
 * Centralises the REST namespace and the `rest_api_init` hook wiring,
 * so every concrete endpoint only has to implement its `routes()`
 * method. Implements {@see Routable} so the route-declaration contract
 * is discoverable at the type level.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Contracts\Routable;
use DuckDev\FourNotFour\Utils\Permission;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Endpoint
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Api
 */
abstract class Endpoint implements Routable {

	/**
	 * REST namespace shared by every plugin endpoint.
	 *
	 * Concrete endpoints register routes under this namespace, so the
	 * full route looks like `/404-to-301/v1/<name>`.
	 *
	 * @since 4.0.0
	 */
	const NAMESPACE = '404-to-301/v1';

	/**
	 * Hook the route registration into `rest_api_init`.
	 *
	 * Subclasses can call `parent::__construct()` to inherit the
	 * hooking, or skip it entirely when they need to override the
	 * timing (for example, to register routes lazily).
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Permission callback that requires the plugin's manage capability.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return bool
	 */
	public function require_access( WP_REST_Request $request ): bool {
		/**
		 * Filter the per-request access check.
		 *
		 * @since 4.0.0
		 *
		 * @param bool            $allowed Default check result.
		 * @param WP_REST_Request $request REST request.
		 */
		return (bool) apply_filters(
			'404_to_301_rest_has_access',
			Permission::has_access(),
			$request
		);
	}

	/**
	 * Wrap response data with a HTTP status code.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $data    Response data.
	 * @param int   $status  HTTP status code.
	 *
	 * @return WP_REST_Response
	 */
	protected function respond( $data, int $status = 200 ): WP_REST_Response {
		return new WP_REST_Response( $data, $status );
	}

	/**
	 * Build a list response that matches WP core's collection convention:
	 *  - body: a bare array of items.
	 *  - headers: `X-WP-Total` + `X-WP-TotalPages`.
	 *
	 * `@wordpress/api-fetch` consumers (and the reference DataView
	 * hooks) read these headers to drive pagination.
	 *
	 * @since 4.0.0
	 *
	 * @param array $items    List of items to return.
	 * @param int   $total    Total number of items across all pages.
	 * @param int   $per_page Items per page (used to derive total pages).
	 *
	 * @return WP_REST_Response
	 */
	protected function collection( array $items, int $total, int $per_page ): WP_REST_Response {
		$response = new WP_REST_Response( array_values( $items ), 200 );
		$response->header( 'X-WP-Total', (string) $total );
		$response->header( 'X-WP-TotalPages', (string) max( 1, (int) ceil( $total / max( 1, $per_page ) ) ) );

		return $response;
	}
}
