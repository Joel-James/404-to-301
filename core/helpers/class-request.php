<?php

namespace DuckDev\WP404\Helpers;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

/**
 * Define the request functionality.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Request {

	/**
	 * Get a value from $_GET global.
	 *
	 * @param string $string  String name.
	 * @param mixed  $default Default value.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function get( $string, $default = false ) {
		$value = filter_input( INPUT_GET, $string );

		if ( ! empty( $value ) ) {
			return $value;
		}

		return $default;
	}

	/**
	 * Get a value from $_POST global.
	 *
	 * @param string $string  String name.
	 * @param mixed  $default Default value.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function post( $string, $default = false ) {
		$value = filter_input( INPUT_POST, $string );

		if ( ! empty( $value ) ) {
			return $value;
		}

		return $default;
	}
}
