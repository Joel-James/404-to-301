<?php
/**
 * Helper utility class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Utils
 * @subpackage Helpers
 */

namespace DuckDev\Redirect\Utils;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Helper
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Utils
 */
class Helpers {

	/**
	 * Get boolean value from string.
	 *
	 * @param mixed $string Value to check.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function get_boolean( $string ) {
		// Accept all possible true values.
		$values = array( 'enabled', '1', 'true', true, 1 );

		return in_array( $string, $values, true );
	}
}
