<?php

namespace DuckDev\WP404\Controllers\Endpoints;

// Direct hit? You must die.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\WP404\Utils\Abstracts\Endpoint;

/**
 * Logs functionality REST endpoint.
 *
 * @link       https://duckdev.com
 * @since      4.0.0
 * @package    Settings
 * @subpackage Endpoint
 *
 * @author     Joel James <me@joelsays.com>
 */
class Logs extends Endpoint {

	/**
	 * API endpoint for the current endpoint.
	 *
	 * @var string $endpoint
	 *
	 * @since 4.0.0
	 */
	private $endpoint = '/logs/';

	/**
	 * Get current API endpoint url.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_url() {
		return rest_url( $this->get_namespace() . $this->endpoint );
	}

	/**
	 * Register the routes for handling settings functionality.
	 *
	 * Register routes:
	 * - v1/settings - GET to get settings.
	 * - v1/settings - POST, PUT, PATCH to update settings.
	 *
	 * @since 4.0.0
	 */
	public function register_routes() {
		// Route to get the logs list.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint, [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_logs' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [],
				],
			]
		);

		// Route to get a single log.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<id>\d+)', [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_log' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'id' => [
							'validate_callback' => function ( $param ) {
								return is_numeric( $param );
							},
						],
					],
				],
			]
		);

		// Route to update a log.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<id>\d+)', [
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_log' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'id' => [
							'validate_callback' => function ( $param ) {
								return is_numeric( $param );
							},
						],
					],
				],
			]
		);

		// Route to delete a log.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<id>\d+)', [
				[
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_log' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'id' => [
							'validate_callback' => function ( $param ) {
								return is_numeric( $param );
							},
						],
					],
				],
			]
		);
	}

	/**
	 * Get the logs list from db.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_logs( $request ) {
		// Get the optional params.
		$page  = $request->get_param( 'page' );
		$size  = $request->get_param( 'per_page' );
		$order = $request->get_param( 'order_by' );

		// Send response.
		return $this->get_response( [
			[
				'id'       => 1,
				'path'     => 'test',
				'date'     => '25-12-2019',
				'referral' => 'none',
				'ip'       => '127.0.0.1',
				'ua'       => 'none',
			],
			[
				'id'       => 2,
				'path'     => 'test/test',
				'date'     => '26-15-2019',
				'referral' => 'none',
				'ip'       => '127.0.0.1',
				'ua'       => 'none',
			],
		] );
	}

	/**
	 * Get a single log item data.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_log( $request ) {
		// Get the log ID.
		$id = $request->get_param( 'id' );

		// Send response.
		return $this->get_response( [] );
	}

	/**
	 * Update a single log item data.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_log( $request ) {
		// Get the log ID.
		$id = $request->get_param( 'id' );

		// Send response.
		return $this->get_response( [] );
	}

	/**
	 * Delete a single log item.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_log( $request ) {
		// Get the log ID.
		$id = $request->get_param( 'id' );

		// Send response.
		return $this->get_response( [] );
	}

	/**
	 * Check if a given request has access to update a setting.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool
	 */
	public function permissions_check( $request ) {
		/**
		 * Filter to modify settings capability.
		 *
		 * @paran string $cap Capability name.
		 *
		 * @since 4.0.0
		 */
		$settings_cap = apply_filters( '404_to_301_settings_capability', 'manage_options' );

		return current_user_can( $settings_cap );
	}
}
