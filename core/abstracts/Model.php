<?php
/**
 * Singleton class for all models.
 *
 * Extend this class whenever possible to make use of common
 * methods.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @since      4.0.0
 * @subpackage Model
 */

namespace DuckDev\DD4T3\Abstracts;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Base
 *
 * @package DuckDev\DD4T3\Abstracts
 */
abstract class Model {

	/**
	 * Singleton constructor.
	 *
	 * Protect the class from being initiated multiple times.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function __construct() {
		// Protect class from initiated multiple times.
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * Classes can override this by extending it.
	 *
	 * @param string $name Table name.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	protected function getTableName( $name ) {
		global $wpdb;

		return $wpdb->prefix . $name;
	}
}
