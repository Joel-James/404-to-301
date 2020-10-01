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

namespace DuckDev\Redirect\Abstracts;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Base
 *
 * @package DuckDev\DD4T3\Abstracts
 */
abstract class Model {

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
	protected function get_table_name( $name ) {
		global $wpdb;

		return $wpdb->prefix . $name;
	}
}
