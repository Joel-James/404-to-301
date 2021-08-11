<?php
/**
 * The plugin permissions class.
 *
 * This class contains the functionality to manage the permissions
 * inside the plugin.
 * Currently, we use only one capability to manage everything.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Permission
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Permission
 *
 * @since   4.0.0
 * @package DuckDev\Redirect
 */
class Permission {

	/**
	 * Capability used to access plugin.
	 *
	 * @var string
	 * @since 4.0.0
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
		 * @param bool $has Has access.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_permission_has_access', $has );
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
		 * @param string $cap Capability.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_permission_get_cap', self::CAPABILITY );
	}
}
