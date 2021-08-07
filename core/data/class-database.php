<?php
/**
 * The plugin permissions class.
 *
 * This class contains the functionality to manage the permissions
 * inside the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Permission
 */

namespace DuckDev\Redirect\Data;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Permission
 *
 * @package DuckDev\Redirect\Controllers
 */
class Database {

	/**
	 * Get available redirect types.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function tables() {
		return array(
			'logs'      => '404_to_301_logs',
			'options'   => '404_to_301_options',
			'redirects' => '404_to_301_redirects',
		);
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * Classes can override this by extending it.
	 *
	 * @param string $table Table key.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public static function table_name( $table ) {
		$tables = self::tables();
		if ( isset( $tables[ $table ] ) ) {
			global $wpdb;

			return $wpdb->prefix . $tables[ $table ];
		}

		return false;
	}
}
