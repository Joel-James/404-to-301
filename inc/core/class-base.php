<?php

namespace DuckDev404\Inc\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Utils\Loader;

/**
 * The core plugin controller class.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Base extends Loader {

	/**
	 * The instance of the called class.
	 *
	 * @since 4.0
	 *
	 * @var object $instance Instance of the class.
	 */
	private static $instance;

	/**
	 * Base constructor.
	 *
	 * Prevent extending classes multiple instances.
	 *
	 * @since 4.0
	 */
	private function __construct() {
		// Nothing to do here.
	}

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance of the called class is loaded.
	 * This should be used whenever possible to avoid create multiple
	 * instances of same class.
	 *
	 * @since 4.0
	 *
	 * @return object
	 */
	public static function instance() {
		// Get the extended class name.
		$class = get_called_class();

		// If instance not set, or not valid, create new.
		if ( is_null( self::$instance ) || ! self::$instance instanceof $class ) {
			self::$instance = new $class();

			// Optional initialization.
			if ( method_exists( self::$instance, 'init' ) ) {
				// Initialize.
				self::$instance->init();
			}
		}

		return self::$instance;
	}

	/**
	 * Setter method.
	 *
	 * Set property and values to class.
	 *
	 * @param string $key   Property to set.
	 * @param mixed  $value Value to assign to the property.
	 *
	 * @since 4.0
	 */
	public function __set( $key, $value ) {
		$this->{$key} = $value;
	}

	/**
	 * Getter method.
	 *
	 * Allows access to extended site properties.
	 *
	 * @param string $key Property to get.
	 *
	 * @since 4.0
	 *
	 * @return mixed Value of the property. Null if not available.
	 */
	public function __get( $key ) {
		// If set, get it.
		if ( isset( $this->{$key} ) ) {
			return $this->{$key};
		}

		return null;
	}
}
