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

/**
 * Class Base
 *
 * @package DuckDev\DD4T3\Abstracts
 */
abstract class Controller extends Base {

	/**
	 * Get boolean value from string.
	 *
	 * @param mixed $value Value to process.
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	protected function get_boolean( $value ) {
		switch ( $value ) {
			case 'enabled':
			case 1:
			case 'true':
			case true:
				$value = true;
				break;
			case 'disabled':
			case 0:
			case 'false':
			case false:
				$value = false;
				break;
		}

		return (bool) $value;
	}
}
