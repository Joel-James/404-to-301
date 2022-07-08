<?php
/**
 * The plugin addon class.
 *
 * This class handles the functionality for Addons.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Addons
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Addon
 *
 * @since   4.0.0
 * @package DuckDev\Redirect
 */
class Addon {

	/**
	 * Check if an addon is active.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $id Add on ID.
	 *
	 * @return mixed|void
	 */
	public function is_active( $id ) {
		$active = false;

		// Make sure the plugin function exists.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Check if the addon plugin is active.
		if ( isset( $addons[ $id ]['plugin'] ) ) {
			$active = is_plugin_active( $addons[ $id ]['plugin'] ) || is_plugin_active_for_network( $addons[ $id ]['plugin'] );
		}

		// @todo Remove this debug code.
		if ( 'logs-exporter' === $id ) {
			$active = true;
		}

		/**
		 * Filter to modify addon active status check.
		 *
		 * @since 4.0.0
		 *
		 * @param array $active Active status.
		 */
		return apply_filters( 'dd4t3_addon_is_active', $active );
	}

	/**
	 * Check if an addon is installed.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $id Add on ID.
	 *
	 * @return mixed|void
	 */
	public function is_installed( $id ) {
		$installed = false;
		$plugins   = get_plugins();
		$addons    = $this->get_addons();

		// Check if the addon plugin is installed.
		if ( isset( $addons[ $id ]['plugin'], $plugins[ $addons[ $id ]['plugin'] ] ) ) {
			$installed = true;
		}

		// @todo Remove this debug code.
		if ( 'logs-exporter' === $id || 'notification-manager' === $id ) {
			$installed = true;
		}

		/**
		 * Filter to modify addon installation status check.
		 *
		 * @since 4.0.0
		 *
		 * @param array $active Installation status.
		 */
		return apply_filters( 'dd4t3_addon_is_installed', $installed );
	}

	/**
	 * Get available addons list.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_addons() {
		$addons = array(
			'logs-cleaner'         => array(
				'title'       => __( 'Logs Cleaner', '404-to-301' ),
				'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '404-to-301' ),
				'link'        => 'https://duckdev.com/products/logs-cleaner/',
				'is_paid'     => true,
				'plugin'      => '404-to-301-logs-cleaner/logs-cleaner.php',
			),
			'logs-exporter'        => array(
				'title'       => __( 'Logs Exporter', '404-to-301' ),
				'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '404-to-301' ),
				'link'        => 'https://duckdev.com/products/logs-exporter/',
				'is_paid'     => false,
				'plugin'      => '404-to-301-log-exporter/log-exporter.php',
			),
			'notification-manager' => array(
				'title'       => __( 'Notification Manager', '404-to-301' ),
				'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', '404-to-301' ),
				'link'        => 'https://duckdev.com/products/notification-manager/',
				'is_paid'     => true,
				'plugin'      => '404-to-301-notification-manager/notification-manager.php',
			),
		);

		/**
		 * Filter the list of addons available.
		 *
		 * @since 4.0.0
		 *
		 * @param array $types Addon list.
		 */
		return apply_filters( 'dd4t3_get_addons', $addons );
	}
}
