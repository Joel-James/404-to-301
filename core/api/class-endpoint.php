<?php
/**
 * API endpoint base class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    API
 * @subpackage Endpoint
 */

namespace RedirectPress\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Request;
use WP_REST_Response;
use RedirectPress\Permission;

/**
 * Class Endpoint
 *
 * @since   4.0.0
 * @package RedirectPress\Api
 */
abstract class Endpoint {

	/**
	 * API endpoint version.
	 *
	 * @var int $version
	 * @since  4.0.0
	 * @access protected
	 */
	protected $version = 1;

	/**
	 * API endpoint version.
	 *
	 * @var int $version
	 * @since  3.2.4
	 * @access protected
	 */
	protected $base = '404-to-301';

	/**
	 * API endpoint namespace.
	 *
	 * @var string $namespace
	 * @since  3.2.4
	 * @access protected
	 */
	protected $namespace;

	/**
	 * Set up API endpoints.
	 *
	 * Namespace will be created based on the base and version.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {
		// Setup namespace of the endpoint.
		$this->namespace = $this->base . '/v' . $this->version;

		// Register endpoints.
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * This should be defined in extending class.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	abstract public function routes();

	/**
	 * Get namespace of the endpoint.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Get current version of the endpoint.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return int
	 */
	protected function get_version() {
		return $this->version;
	}

	/**
	 * Get formatted response for the current request.
	 *
	 * @param array $data    Response data.
	 * @param bool  $success Is request success.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return WP_REST_Response
	 */
	protected function get_response( $data = array(), $success = true ) {
		// Response status.
		$status = $success ? 200 : 400;

		return new WP_REST_Response(
			array(
				'success' => $success,
				'data'    => $data,
			),
			$status
		);
	}

	/**
	 * Check if the current user can manage settings using API.
	 *
	 * Use this to check if current user has the capability to
	 * manage our plugin.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 * @todo Change the check once ready for production.
	 *
	 * @return bool
	 */
	public function has_access( $request ) {
		// Check capability.
		$capable = Permission::has_access();

		/**
		 * Filter to modify the access check for rest endpoint.
		 *
		 * @param bool            $capable Is capable.
		 * @param WP_REST_Request $request Request.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'redirectpress_rest_has_access', true, $request );
	}

	/**
	 * Check if current user is logged in to access API.
	 *
	 * Use this method to check if current user is logged in.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_logged_in( $request ) {
		// Check if user is logged in.
		$capable = is_user_logged_in();

		/**
		 * Filter to modify the login check for rest endpoint.
		 *
		 * @param bool            $capable Is capable.
		 * @param WP_REST_Request $request Request.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_rest_is_logged_in', $capable, $request );
	}

	/**
	 * Retrieves a parameter from the request.
	 *
	 * Alias method to get default value if not set.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @param string          $key     Parameter name.
	 * @param mixed           $default Default value.
	 *
	 * @since 4.4.0
	 *
	 * @return mixed|null Value if set, null otherwise.
	 */
	protected function get_param( $request, $key, $default = null ) {
		$value = $request->get_param( $key );

		if ( null === $value ) {
			$value = $default;
		}

		return $value;
	}
}
