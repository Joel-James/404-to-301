<?php

namespace DuckDev404\Core\Utils\Abstracts;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Base class for all classes.
 *
 * @link   https://duckdev.com
 * @since  4.0.0
 *
 * @author Joel James <me@joelsays.com>
 */
abstract class Base extends Singleton {

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
