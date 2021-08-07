<?php
/**
 * Singleton class for all classes.
 *
 * Extend this class whenever possible to avoid multiple instances
 * of the same classes being created.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @since      4.0.0
 * @subpackage Core
 */

namespace DuckDev\Redirect\Utils\Abstracts;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Permission;

/**
 * Class Base
 *
 * @package DuckDev\Redirect\Abstracts
 */
abstract class Endpoint extends Base {

	/**
	 * API endpoint version.
	 *
	 * @var int $version
	 *
	 * @since 3.2.4
	 */
	protected $version = 1;

	/**
	 * API endpoint version.
	 *
	 * @var int $version
	 *
	 * @since 3.2.4
	 */
	private $base = '404-to-301';

	/**
	 * API endpoint namespace.
	 *
	 * @var string $namespace
	 *
	 * @since 3.2.4
	 */
	private $namespace;

	/**
	 * Set up WordPress hooks and filters
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function init() {
		// Setup namespace of the endpoint.
		$this->namespace = $this->base . '/v' . $this->version;

		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * This should be defined in extending class.
	 *
	 * @since 3.2.4
	 */
	abstract public function routes();

	/**
	 * Get namespace of the endpoint.
	 *
	 * @since 3.2.4
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Get current version of the endpoint.
	 *
	 * @since 3.2.4
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get formatted response for the current request.
	 *
	 * @param array $data    Response data.
	 * @param bool  $success Is request success.
	 *
	 * @since 3.2.4
	 *
	 * @return WP_REST_Response
	 */
	public function get_response( $data = array(), $success = true ) {
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
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function check_settings_permission( $request ) {
		// Check capability.
		$capable = Permission::has_access();

		/**
		 * Filter to modify the settings capability check for API.
		 *
		 * @paran bool $capable Is capable.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_rest_check_settings_permission', $capable );
	}

	/**
	 * Check if current user is logged in to access API.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function check_loggedin_permission( $request ) {
		// Check if user is logged in.
		$capable = is_user_logged_in();

		/**
		 * Filter to modify the loggedin capability check for API.
		 *
		 * @paran bool $capable Is capable.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_rest_check_loggedin_permission', $capable );
	}
}
