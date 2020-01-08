<?php

namespace DuckDev\WP404\Utils\Abstracts;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

/**
 * Singleton class for all classes.
 *
 * @link   https://duckdev.com
 * @since  3.2.0
 *
 * @author Joel James <me@joelsays.com>
 */
abstract class Model {

	/**
	 * Singleton constructor.
	 *
	 * Protect the class from being initiated multiple times.
	 *
	 * @since 3.2.0
	 */
	protected function __construct() {
		// Protect class from initiated multiple times.
	}

	/**
	 * Instance obtaining method.
	 *
	 * @since 3.2.0
	 *
	 * @return static Called class instance.
	 */
	public static function get( $id ) {
		static $instances = [];

		// @codingStandardsIgnoreLine Plugin-backported
		$called_class_name = get_called_class();

		if ( ! isset( $instances[$called_class_name][$id] ) ) {
			if ( ! isset( $instances[$called_class_name] ) ) {
				$instances[$called_class_name] = [];
			}

			$instances[$called_class_name][$id] = new $called_class_name();

			// Run the initialization method.
			if ( method_exists( $instances[$called_class_name][$id], 'init' ) ) {
				$instances[$called_class_name][$id]->init( $id );
			}
		}

		return $instances[$called_class_name][$id];
	}

	/**
	 * Setter method.
	 *
	 * Set property and values to class.
	 *
	 * @param string $key   Property to set.
	 * @param mixed  $value Value to assign to the property.
	 *
	 * @since 4.0.0
	 *
	 * @return void
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
	 * @since 4.0.0
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

	/**
	 * Get network admin flag.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_network() {
		return is_network_admin();
	}
}
