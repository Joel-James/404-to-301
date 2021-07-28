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

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class Permission
 *
 * @package DuckDev\Redirect\Controllers
 */
class Permission extends Base {

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
		return apply_filters( 'dd404_permission_settings_cap', 'manage_options' );
	}

	/**
	 * Get the capability to manage logs.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function logs_cap() {
		/**
		 * Filter hook to change the logs capability.
		 *
		 * @param string $cap Capability.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_permission_logs_cap', 'manage_options' );
	}

	/**
	 * Check if current user has the capability for an action.
	 *
	 * @param string $type Type.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function user_can( $type = 'settings' ) {
		switch ( $type ) {
			case 'settings':
				$capable = current_user_can( self::settings_cap() );
				break;
			case 'logs':
				$capable = current_user_can( self::logs_cap() );
				break;
			default:
				$capable = false;
				break;
		}

		/**
		 * Filter hook to modify capability check.
		 *
		 * @param string $capable Capable.
		 * @param string $type    Type.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_permission_user_can', $capable, $type );
	}
}
