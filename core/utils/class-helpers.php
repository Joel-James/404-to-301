<?php
/**
 * Helper utility class.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Utils
 * @subpackage Helpers
 */

namespace DuckDev\FourNotFour\Utils;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Helper
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Utils
 */
class Helpers {

	/**
	 * Get boolean value from string.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param mixed $string Value to check.
	 *
	 * @return bool
	 */
	public static function get_boolean( $string ) {
		// Accept all possible true values.
		$values = array( 'enabled', '1', 'true', true, 1 );

		return in_array( $string, $values, true );
	}

	/**
	 * Get a value from the $_GET global variable.
	 *
	 * Use this to sanitize and get the input values.
	 *
	 * @see    https://www.php.net/manual/en/function.filter-input.php
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $name   Name of the input item.
	 * @param mixed  $filter Sanitization to use (String sanitization by default).
	 *                       See https://www.php.net/manual/en/filter.filters.php.
	 *
	 * @return mixed
	 */
	public static function input_get( $name, $filter = FILTER_UNSAFE_RAW ) {
		return filter_input( INPUT_GET, $name, $filter );
	}

	/**
	 * Get a value from the $_POST global variable.
	 *
	 * Use this to sanitize and get the post data.
	 *
	 * @see    https://www.php.net/manual/en/function.filter-input.php
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $name   Name of the input item.
	 * @param mixed  $filter Sanitization to use (String sanitization by default).
	 *                       See https://www.php.net/manual/en/filter.filters.php.
	 *
	 * @return mixed
	 */
	public static function input_post( $name, $filter = FILTER_UNSAFE_RAW ) {
		return filter_input( INPUT_POST, $name, $filter );
	}
}
