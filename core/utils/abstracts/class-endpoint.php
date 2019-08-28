<?php

namespace DuckDev\WP404\Utils\Abstracts;

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
	 * Register the routes for the objects of the controller.
	 *
	 * This should be defined in extending class.
	 *
	 * @since 4.0.0
	 */
	public abstract function register_routes();
}
