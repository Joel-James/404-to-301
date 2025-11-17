<?php
/**
 * The plugin permissions class.
 *
 * This class contains the functionality to manage the permissions
 * inside the plugin.
 * Currently, we use only one capability to manage everything.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Permission
 */

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Permission
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
class Permission {

	/**
	 * Capability used to access plugin.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	const CAPABILITY = 'manage_options';

	/**
	 * Check if current user has access to our plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @todo   Use different capabilities for logs,redirects and settings.
	 *
	 * @return bool
	 */
	public static function has_access() {
		// Check if current user can.
		$has = current_user_can( self::get_cap() );

		/**
		 * Filter hook to modify capability check.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $has Has access.
		 */
		return apply_filters( '404_to_301_permission_has_access', $has );
	}

	/**
	 * Get the capability to manage logs.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_cap() {
		/**
		 * Filter hook to change the plugin capability.
		 *
		 * @since 4.0.0
		 *
		 * @param string $cap Capability.
		 */
		return apply_filters( '404_to_301_permission_get_cap', self::CAPABILITY );
	}
}
