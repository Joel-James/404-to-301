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
use DuckDev\WP404\Models;
use IronBound\DB\Manager;

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
		$logs = [];

		// Get the optional params.
		$page     = $this->get_param( $request, 'page', 1 );
		$size     = $this->get_param( $request, 'per_page', 25 );
		$order    = $this->get_param( $request, 'sort_order', 'desc' );
		$order_by = $this->get_param( $request, 'sort_by', 'id' );
		$search   = $this->get_param( $request, 'search', '' );

		$query_args = [
			'page'                => $page,
			'items_per_page'      => $size,
			'sql_calc_found_rows' => true,
			'url_search'          => $search,
		];

		// Sorting is required only when column and order is given.
		if ( ! empty( $order ) && ! empty( $order_by ) ) {
			$query_args['order'] = [
				$order_by => $order,
			];
		}

		$query = new Queries\Log( $query_args );

		foreach ( $query->get_results() as $id => $log ) {
			$logs[] = $log->to_array();
		}

		// Send response.
		return $this->get_response( [
			'items' => $logs,
			'total' => $query->get_total_items(),
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

		/*$log = Models\Log::get( $id );

		if ( empty( $log ) ) {
			$log = [];
		} else {
			$log = $log->to_array();
		}*/

		$query = Manager::make_simple_query_object( '404_to_301' );
		$log   = $query->get( $id );

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

		$log = Models\Log::get( $id );

		if ( ! empty( $log ) ) {
			$log->delete();
		}

		// Send response.
		return $this->get_response( [
			'message' => __( 'Log deleted successfully', '404-to-301' ),
		] );
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

		$ids = explode( ',', $ids );

		$query = new Queries\Log( [
			'sql_calc_found_rows' => true,
		] );

		foreach ( $query->get_results() as $id => $log ) {
			$logs[] = $log->to_array();
		}

		// Send response.
		return $this->get_response( [] );
	}
}
