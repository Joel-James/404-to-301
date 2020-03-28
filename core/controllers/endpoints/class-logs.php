<?php

namespace DuckDev\WP404\Controllers\Endpoints;

// Direct hit? You must die.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\WP404\Utils\Abstracts\Endpoint;
use DuckDev\WP404\Database\Queries;
use DuckDev\WP404\Database\Models\Log;

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
					'args'                => [
						'search'   => [
							'required'    => false,
							'description' => __( 'String to search within error URLs.', 'ga_trans' ),
							'type'        => 'string',
						],
						'status'   => [
							'required'    => false,
							'description' => __( 'The status of the log to get (1 default).', 'ga_trans' ),
							'type'        => 'integer',
							'enum'        => [ 0, 1 ],
						],
						'page'     => [
							'required'    => false,
							'description' => __( 'Current page number (1 default).', 'ga_trans' ),
							'type'        => 'integer',
						],
						'per_page' => [
							'required'    => false,
							'description' => __( 'Number of items per page (25 default).', 'ga_trans' ),
							'type'        => 'integer',
						],
						'order'    => [
							'required'    => false,
							'description' => __( 'Sorting order (asc or desc).', 'ga_trans' ),
							'type'        => 'string',
							'enum'        => [
								'asc',
								'desc',
							],
						],
						'order_by' => [
							'required'    => false,
							'description' => __( 'A field to order the result.', 'ga_trans' ),
							'type'        => 'string',
							'enum'        => [
								'date',
								'id',
							],
						],
						'group_by' => [
							'required'    => false,
							'description' => __( 'A field to group the result.', 'ga_trans' ),
							'type'        => 'string',
							'enum'        => [
								'', // Empty.
								'date',
								'url',
								'ref',
								'ip',
								'ua',
							],
						],
					],
				],
				[
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_logs' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'ids' => [
							'required'    => true,
							'description' => __( 'The log ids to delete, separated by comma.', 'ga_trans' ),
							'type'        => 'array',
						],
					],
				],
			]
		);

		// Route to handle a log.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<id>\d+)', [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_log' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'id' => [
							'required'    => true,
							'description' => __( 'The log id to view.', 'ga_trans' ),
							'type'        => 'integer',
						],
					],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_log' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'id' => [
							'required'    => true,
							'description' => __( 'The log id to update.', 'ga_trans' ),
							'type'        => 'integer',
						],
					],
				],
				[
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_log' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'id' => [
							'required'    => true,
							'description' => __( 'The log id to delete.', 'ga_trans' ),
							'type'        => 'integer',
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
		// Set the optional params.
		$query_args = [
			'status'   => $this->get_param( $request, 'status', 1, 'intval' ),
			'page'     => $this->get_param( $request, 'page', 1, 'intval' ),
			'per_page' => $this->get_param( $request, 'per_page', 25, 'intval' ),
			'order'    => $this->get_param( $request, 'order', 'desc' ),
			'order_by' => $this->get_param( $request, 'order_by', 'id' ),
			'group_by' => $this->get_param( $request, 'group_by', '' ),
			'search'   => $this->get_param( $request, 'search', '' ),
		];

		// Get query object.
		$result = Queries\Log::_get()->get_logs( $query_args );

		// Send response.
		return $this->get_response( $result );
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
		$id = (int) $request->get_param( 'id' );

		$log = Queries\Log::_get()->get_log( $id );

		// Empty log.
		if ( empty( $log ) ) {
			return $this->get_response( [
				'message' => __( 'Sorry. No error log found for the given ID.', '404-to-301' ),
			], false );
		}

		// Send response.
		return $this->get_response( $log );
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

		$success = $log = Queries\Log::_get()->delete_log( $id );

		if ( $success ) {
			// Send response.
			return $this->get_response( [
				'message' => __( 'Log deleted successfully', '404-to-301' ),
			] );
		} else {
			// Send response.
			return $this->get_response( [
				'message' => __( 'Sorry. Could not delete the log.', '404-to-301' ),
			], false );
		}
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
	public function delete_logs( $request ) {
		// Get the log ID.
		$ids = $request->get_param( 'ids' );

		// Delete logs.
		$success = false;

		if ( $success ) {
			// Send response.
			return $this->get_response( [
				'message' => __( 'Log deleted successfully', '404-to-301' ),
			] );
		} else {
			// Send response.
			return $this->get_response( [
				'message' => $ids,
			], false );
		}
	}
}
