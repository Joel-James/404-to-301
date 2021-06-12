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

namespace DuckDev\Redirect\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Controller;

/**
 * Class Permission
 *
 * @package DuckDev\Redirect\Controllers
 */
class Permission extends Controller {

	/**
	 * Get the capability to manage settings.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function settings_cap() {
		/**
		 * Filter hook to change the settings capability.
		 *
		 * @param string $cap Capability.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_settings_cap', 'manage_options' );
	}

	/**
	 * Check if current user has the capability to manage settings.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function has_settings_cap() {
		// Check if capable.
		$capable = current_user_can( self::settings_cap() );

		/**
		 * Filter hook to modify capability check.
		 *
		 * @param string $capable Capable.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_has_settings_cap', $capable );
	}
}
