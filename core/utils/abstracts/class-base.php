<?php

namespace DuckDev\WP404\Utils\Abstracts;

// Direct hit? Rest in peace..
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
	 * This is an empty constructor.
	 *
	 * @since 4.0.0
	 */
	protected function __construct() {
		parent::__construct();
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
