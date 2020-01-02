<?php

namespace DuckDev\WP404\Utils\Abstracts;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Base class for all endpoint classes.
 *
 * @link       https://duckdev.com
 * @since      4.0.0
 * @package    Endpoint
 * @subpackage REST_Controller
 *
 * @author     Joel James <me@joelsays.com>
 */
abstract class Endpoint extends Base {

	/**
	 * API endpoint version.
	 *
	 * @var int $version
	 *
	 * @since 4.0.0
	 */
	protected $version = 1;

	/**
	 * API endpoint namespace.
	 *
	 * @var string $namespace
	 *
	 * @since 4.0.0
	 */
	private $namespace;

	/**
	 * Endpoint constructor.
	 *
	 * We need to register the routes here.
	 *
	 * @since 4.0.0
	 */
	protected function __construct() {
		parent::__construct();

		// Setup namespace of the endpoint.
		$this->namespace = DD404_SLUG . '/v' . $this->version;

		// If the single instance hasn't been set, set it now.
		$this->register_hooks();
	}

	/**
	 * Set up WordPress hooks and filters
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Get namespace of the endpoint.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Get current version of the endpoint.
	 *
	 * @since 4.0.0
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
	 * @since 4.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function get_response( $data = [], $success = true ) {
		// Response status.
		$status = $success ? 200 : 400;

		return new WP_REST_Response( array(
			'success' => $success,
			'data'    => $data,
		), $status );
	}

	/**
	 * Check if a given request has access to update a setting.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool
	 */
	public function permissions_check( $request ) {
		$capable = current_user_can( 'manage_options' );

		/**
		 * Filter to modify settings capability.
		 *
		 * @paran string $cap Capability name.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_settings_permissions_check', true );
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * This should be defined in extending class.
	 *
	 * @since 4.0.0
	 */
	public abstract function register_routes();
}
