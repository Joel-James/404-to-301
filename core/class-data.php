<?php
/**
 * The plugin data class.
 *
 * This class contains the data objects for the plugin.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Data
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Data
 *
 * @since   4.0.0
 * @package DuckDev\Redirect
 */
class Data {

	/**
	 * Get available redirect types.
	 *
	 * To add or remove redirect types, use dd4t3_redirect_types filter.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return mixed|void
	 */
	public static function redirect_types() {
		$types = array(
			301 => __( '301 - Moved Permanently', '404-to-301' ),
			302 => __( '302 - Found', '404-to-301' ),
			303 => __( '303 - See Other', '404-to-301' ),
			304 => __( '304 - Not Modified', '404-to-301' ),
			307 => __( '307 - Temporary Redirect', '404-to-301' ),
			308 => __( '308 - Permanent Redirect', '404-to-301' ),
		);

		/**
		 * Filter to add or remove redirect types.
		 *
		 * @param array $types Redirect types.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_redirect_types', $types );
	}

	/**
	 * Get available addons list.
	 *
	 * To add or remove an addon, use dd4t3_addons_list filter.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function addons() {
		$types = array(
			array(
				'slug'        => 'logs-cleaner',
				'title'       => __( 'Logs Cleaner', '404-to-301' ),
				'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '404-to-301' ),
				'link'        => 'https://duckdev.com/products/logs-cleaner/',
				'is_paid'     => true,
			),
			array(
				'slug'        => 'logs-exporter',
				'title'       => __( 'Logs Exporter', '404-to-301' ),
				'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '404-to-301' ),
				'link'        => 'https://duckdev.com/products/logs-exporter/',
				'is_paid'     => false,
			),
			array(
				'slug'        => 'notification-manager',
				'title'       => __( 'Notification Manager', '404-to-301' ),
				'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '404-to-301' ),
				'link'        => 'https://duckdev.com/products/notification-manager/',
				'is_paid'     => true,
			),
		);

		/**
		 * Filter to add or remove addons.
		 *
		 * @param array $types Addon list.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_addons_list', $types );
	}
}
