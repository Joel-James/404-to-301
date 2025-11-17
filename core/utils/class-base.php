<?php
/**
 * Singleton class for all classes.
 *
 * Extend this class whenever possible to avoid multiple instances
 * of the same classes being created.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Base
 */

namespace DuckDev\FourNotFour\Utils;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Base
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Utils
 */
abstract class Base {

	/**
	 * Singleton constructor.
	 *
	 * Protect the class from being initiated multiple times.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function __construct() {
		// Protect class from initiated multiple times.
	}

	/**
	 * Instance obtaining method.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return static Called class instance.
	 */
	public static function instance() {
		static $instances = array();

		$called_class_name = get_called_class();

		// Only if not already exist.
		if ( ! isset( $instances[ $called_class_name ] ) ) {
			$instances[ $called_class_name ] = new $called_class_name();

			// Optionally initialize the class.
			if ( method_exists( $instances[ $called_class_name ], 'init' ) ) {
				$instances[ $called_class_name ]->init();
			}
		}

		return $instances[ $called_class_name ];
	}
}
