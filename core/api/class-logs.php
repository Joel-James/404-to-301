<?php
/**
 * The logs API endpoint class.
 *
 * This class handles the API endpoint for logs management.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Utils\Endpoint;

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
	 * @var string $endpoint
	 * @since  4.0.0
	 * @access private
	 */
	private $endpoint = '/logs';

	/**
	 * Register the routes for handling logs.
	 *
	 * Available endpoints:
	 * - /wp-json/404-to-301/v1/logs/ - GET
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function routes() {
		// List logs.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_logs' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'search'    => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Search term to filter logs.', '404-to-301' ),
						),
						'search_by' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'url', 'ip', 'referrer', 'agent', 'method' ),
							'description' => __( 'Search field to filter logs.', '404-to-301' ),
						),
						'group_by'  => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'url', 'ip' ),
							'description' => __( 'Group results by.', '404-to-301' ),
						),
						'order_by'  => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'url', 'ip', 'referrer', 'agent' ),
							'description' => __( 'Order by field name.', '404-to-301' ),
						),
						'order'     => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'asc', 'desc' ),
							'description' => __( 'Order for the results (asc or desc)', '404-to-301' ),
						),
						'per_page'  => array(
							'type'        => 'integer',
							'description' => __( 'No of logs per page.', '404-to-301' ),
						),
						'page'      => array(
							'type'        => 'integer',
							'description' => __( 'Current page number.', '404-to-301' ),
						),
					),
				),
			)
		);

		// Log item.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_log' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to get the details.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_log' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to delete.', '404-to-301' ),
						),
					),
				),
			)
		);

		// Log options.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<id>\d+)/options/',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_options' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to get the options.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_options' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'id'       => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to update the options.', '404-to-301' ),
						),
						'redirect' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'global', 'enabled', 'disabled' ),
							'description' => __( 'Change the status of redirect for this log.', '404-to-301' ),
						),
						'log'      => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'global', 'enabled', 'disabled' ),
							'description' => __( 'Change the status of logging for this log.', '404-to-301' ),
						),
						'email'    => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'global', 'enabled', 'disabled' ),
							'description' => __( 'Change the status of email notification for this log.', '404-to-301' ),
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
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function get_logs( $request ) {
		// Get parameters.
		$search    = $request->get_param( 'search' );
		$search_by = $request->get_param( 'search_by' );

		// Get all logs.
		return $this->get_response();
	}

	/**
	 * Get a single log item.
	 *
	 * Log options will also be available.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function get_log( $request ) {
		// Get parameters.
		$id = $request->get_param( 'id' );

		return $this->get_response();
	}

	/**
	 * Delete a single log item.
	 *
	 * Log options will also be deleted.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function delete_log( $request ) {
		// Get parameters.
		$id = $request->get_param( 'id' );

		return $this->get_response();
	}

	/**
	 * Get a single log options.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function get_options( $request ) {
		// Get parameters.
		$id = $request->get_param( 'id' );

		return $this->get_response();
	}

	/**
	 * Get a single log options.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function update_options( $request ) {
		// Get parameters.
		$id       = $request->get_param( 'id' );
		$redirect = $request->get_param( 'redirect' );
		$log      = $request->get_param( 'log' );
		$email    = $request->get_param( 'email' );

		return $this->get_response();
	}
}
