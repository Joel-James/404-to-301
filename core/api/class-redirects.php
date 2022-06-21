<?php
/**
 * The redirect API endpoint class.
 *
 * This class handles the API endpoint for redirects management.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Endpoint
 * @subpackage Redirects
 */

namespace DuckDev\Redirect\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Data;
use DuckDev\Redirect\Models;

/**
 * Class Redirects
 *
 * @since   4.0.0
 * @extends Endpoint
 * @package DuckDev\Redirect\Api
 */
class Redirects extends Endpoint {

	/**
	 * API endpoint for the current api.
	 *
	 * @since  4.0.0
	 * @access private
	 * @var string $endpoint
	 */
	private $endpoint = '/redirects';

	/**
	 * Register the routes for handling logs.
	 *
	 * Available endpoints:
	 * - /wp-json/404-to-301/v1/redirects/ - GET
	 * - /wp-json/404-to-301/v1/redirects/{id} - GET
	 * - /wp-json/404-to-301/v1/redirects/{id} - POST
	 * - /wp-json/404-to-301/v1/redirects/{id} - DELETE
	 * - /wp-json/404-to-301/v1/redirects/get - GET
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function routes() {
		// Search and list redirects.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_redirects' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'search'         => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Search term to filter redirects.', '404-to-301' ),
						),
						'search_columns' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'source', 'destination', 'code', 'type', 'status' ),
							'description' => __( 'Search fields to filter redirects. Leave empty to search all possible fields.', '404-to-301' ),
						),
						'orderby'        => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'source', 'destination', 'code', 'type', 'status' ),
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
							'required'    => false,
							'description' => __( 'No of redirects per page.', '404-to-301' ),
						),
						'page'           => array(
							'type'        => 'integer',
							'required'    => false,
							'description' => __( 'Current page number.', '404-to-301' ),
						),
					),
				),
				// Create a redirect.
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_redirect' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'source'      => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Redirect source.', '404-to-301' ),
						),
						'destination' => array(
							'type'              => 'string',
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return esc_url_raw( $param ) === $param;
							},
							'description'       => __( 'Redirect destination. Should be a full URL.', '404-to-301' ),
						),
						'code'        => array(
							'type'        => 'integer',
							'required'    => false,
							'enum'        => array_keys( Data::redirect_types() ),
							'description' => __( 'Redirect type code.', '404-to-301' ),
						),
						'type'        => array(
							'type'        => 'integer',
							'required'    => false,
							'enum'        => array( 'url', '404' ),
							'description' => __( 'Redirect type (url or 404).', '404-to-301' ),
						),
						'status'      => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'enabled', 'disabled', 'ignored' ),
							'description' => __( 'Email status.', '404-to-301' ),
						),
					),
				),
			)
		);

		// Manage single redirect item using ID.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '/(?P<redirect_id>\d+)',
			array(
				// Get redirect.
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_redirect' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'redirect_id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Redirect ID to get the details.', '404-to-301' ),
						),
					),
				),
				// Update redirect.
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_redirect' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'redirect_id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Redirect ID to update.', '404-to-301' ),
						),
						'source'      => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Redirect source.', '404-to-301' ),
						),
						'destination' => array(
							'type'              => 'string',
							'required'          => false,
							'validate_callback' => function ( $param ) {
								return esc_url_raw( $param ) === $param;
							},
							'description'       => __( 'Redirect destination. Should be a full URL.', '404-to-301' ),
						),
						'code'        => array(
							'type'        => 'integer',
							'required'    => false,
							'enum'        => array_keys( Data::redirect_types() ),
							'description' => __( 'Redirect type code.', '404-to-301' ),
						),
						'status'      => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array( 'enabled', 'disabled', 'ignored' ),
							'description' => __( 'Email status.', '404-to-301' ),
						),
					),
				),
				// Delete redirect.
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_redirect' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'redirect_id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Redirect ID to delete.', '404-to-301' ),
						),
					),
				),
			)
		);

		// Get log by source url.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '/get',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_by_source' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'source' => array(
							'type'        => 'string',
							'required'    => true,
							'description' => __( 'Source URL to get the log for.', '404-to-301' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Get the list of redirects.
	 *
	 * These redirects can be filtered using available params.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_redirects( $request ) {
		// Get parameters.
		$search    = $request->get_param( 'search' );
		$search_by = $request->get_param( 'search_columns' );
		$order_by  = $request->get_param( 'order_by' );
		$order     = $request->get_param( 'order' );

		// Allowed column names to filter.
		$allowed_cols = array( 'source', 'destination', 'code', 'type', 'status' );

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

		// Get logs.
		$logs = Models\Redirects::instance()->get_redirects( $args );

		// Get all redirects.
		return $this->get_response( $logs );
	}

	/**
	 * Get a single redirect item.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_redirect( $request ) {
		// Get parameters.
		$redirect_id = $request->get_param( 'redirect_id' );

		// Get redirect.
		$log = Models\Redirects::instance()->get( $redirect_id );

		return $this->get_response( $log );
	}

	/**
	 * Get a single redirect item by url.
	 *
	 * Useful to check if a redirect exist for a url.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_by_source( $request ) {
		// Get parameters.
		$url = $request->get_param( 'url' );

		$redirect = array();

		// Get redirect.
		if ( ! empty( $url ) ) {
			$redirect = Models\Redirects::instance()->get_by_source( $url );
		}

		return $this->get_response( $redirect );
	}

	/**
	 * Delete a single redirect item.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function update_redirect( $request ) {
		$data = array();

		// Get parameters.
		$redirect_id = $request->get_param( 'redirect_id' );

		// Fields to update.
		$fields = array( 'visits', 'redirect', 'log', 'email' );

		// Set values.
		foreach ( $fields as $field ) {
			if ( $request->offsetExists( $field ) ) {
				$data[ $field ] = (int) $request->get_param( $field );
			}
		}

		// Update if data is not empty.
		if ( ! empty( $redirect_id ) && ! empty( $data ) ) {
			return $this->get_response( Models\Redirects::instance()->update( $redirect_id, $data ) );
		}

		// Error response.
		return $this->get_response( array(), false );
	}

	/**
	 * Delete a single redirect item.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function delete_redirect( $request ) {
		// Get parameters.
		$redirect_id = $request->get_param( 'redirect_id' );

		// Delete redirects.
		$success = Models\Redirects::instance()->delete( $redirect_id );

		return $this->get_response( array(), $success );
	}
}
