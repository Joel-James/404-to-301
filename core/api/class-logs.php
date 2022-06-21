<?php
/**
 * The logs API endpoint class.
 *
 * This class handles the API endpoint for logs management.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Endpoint
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Models;

/**
 * Class Logs
 *
 * @since   4.0.0
 * @extends Endpoint
 * @package DuckDev\Redirect\Api
 */
class Logs extends Endpoint {

	/**
	 * API endpoint for the current api.
	 *
	 * @since  4.0.0
	 * @access private
	 * @var string $endpoint
	 */
	private $endpoint = '/logs';

	/**
	 * Register the routes for handling logs.
	 *
	 * Available endpoints:
	 * - /wp-json/404-to-301/v1/logs/ - GET
	 * - /wp-json/404-to-301/v1/logs/{id} - GET
	 * - /wp-json/404-to-301/v1/logs/{id} - POST
	 * - /wp-json/404-to-301/v1/logs/{id} - DELETE
	 * - /wp-json/404-to-301/v1/logs/get - GET
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function routes() {
		// Search and list logs.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_logs' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'search'         => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Search term to filter logs.', '404-to-301' ),
						),
						'search_columns' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'url', 'ip', 'referrer', 'agent' ),
							'description' => __( 'Search fields to filter logs. Leave empty to search all possible fields.', '404-to-301' ),
						),
						'groupby'        => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'url', 'ip', 'referrer', 'agent' ),
							'description' => __( 'Group results by.', '404-to-301' ),
						),
						'orderby'        => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'url', 'ip', 'referrer', 'agent' ),
							'description' => __( 'Order by field name.', '404-to-301' ),
						),
						'order'          => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'asc', 'desc' ),
							'description' => __( 'Order for the results (asc or desc)', '404-to-301' ),
						),
						'number'         => array(
							'type'        => 'integer',
							'description' => __( 'No of logs per page.', '404-to-301' ),
						),
						'page'           => array(
							'type'        => 'integer',
							'description' => __( 'Current page number.', '404-to-301' ),
						),
					),
				),
			)
		);

		// Manage single log item using ID.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '/(?P<log_id>\d+)',
			array(
				// Get log.
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_log' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'log_id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to get the details.', '404-to-301' ),
						),
					),
				),
				// Update log.
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_log' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'log_id'          => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to update.', '404-to-301' ),
						),
						'redirect_status' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'global', 'enabled', 'disabled' ),
							'description' => __( 'Redirect status.', '404-to-301' ),
						),
						'log_status'      => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'global', 'enabled', 'disabled' ),
							'description' => __( 'Log status.', '404-to-301' ),
						),
						'email_status'    => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'global', 'enabled', 'disabled' ),
							'description' => __( 'Email status.', '404-to-301' ),
						),
					),
				),
				// Delete log.
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_log' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'log_id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to delete.', '404-to-301' ),
						),
					),
				),
			)
		);

		// Get log by 404 url.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '/url',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_by_url' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'url' => array(
							'type'        => 'string',
							'required'    => true,
							'description' => __( '404 URL to get the log for.', '404-to-301' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Get the list of logs.
	 *
	 * These logs can be filtered using available params.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_logs( $request ) {
		// Get parameters.
		$search    = $request->get_param( 'search' );
		$search_by = $request->get_param( 'search_columns' );
		$group_by  = $request->get_param( 'group_by' );
		$order_by  = $request->get_param( 'order_by' );
		$order     = $request->get_param( 'order' );

		// Allowed column names to filter.
		$allowed_cols = array( 'url', 'ip', 'referrer', 'agent' );

		// Query arguments.
		$args = array(
			'number' => $this->get_param( $request, 'number', 100 ),
			'page'   => $this->get_param( $request, 'page', 1 ),
		);

		// Set up search filter.
		if ( $search ) {
			$args['search']         = (string) $search;
			$args['search_columns'] = $search_by ? (array) $search_by : $allowed_cols;
		}

		// Set up order.
		if ( $order_by ) {
			$args['orderby'] = (string) $order_by;
			$args['order']   = 'desc' === $order ? 'DESC' : 'ASC';
		}

		// Set up group.
		if ( $group_by ) {
			$args['groupby'] = (string) $group_by;
		}

		// Get logs.
		$logs = Models\Logs::instance()->get_logs( $args );

		// Get all logs.
		return $this->get_response( $logs );
	}

	/**
	 * Get a single log item.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_log( $request ) {
		// Get parameters.
		$log_id = $request->get_param( 'log_id' );

		// Get log.
		$log = Models\Logs::instance()->get( $log_id );

		return $this->get_response( $log );
	}

	/**
	 * Get a single log item by url.
	 *
	 * Useful to check if log exist for a url.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_by_url( $request ) {
		// Get parameters.
		$url = $request->get_param( 'url' );

		$log = array();

		// Get log.
		if ( ! empty( $url ) ) {
			$log = Models\Logs::instance()->get_by_url( $url );
		}

		return $this->get_response( $log );
	}

	/**
	 * Update a singe log item.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function update_log( $request ) {
		$data = array();

		// Get parameters.
		$log_id = $request->get_param( 'log_id' );

		// Fields to update.
		$fields = array( 'redirect_status', 'log_status', 'email_status' );

		// No. of hits to the url.
		foreach ( $fields as $field ) {
			if ( $request->offsetExists( $field ) ) {
				$data[ $field ] = $request->get_param( $field );
			}
		}

		// Update if data is not empty.
		if ( ! empty( $log_id ) && ! empty( $data ) ) {
			return $this->get_response( Models\Logs::instance()->update( $log_id, $data ) );
		}

		// Error response.
		return $this->get_response( array(), false );
	}

	/**
	 * Delete a single log item.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function delete_log( $request ) {
		// Get parameters.
		$log_id = $request->get_param( 'log_id' );

		// Delete log.
		$success = Models\Logs::instance()->delete( $log_id );

		return $this->get_response( array(), $success );
	}
}
