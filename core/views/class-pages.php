<?php
/**
 * The plugin pages view class.
 *
 * This class handles the admin pages views for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Pages
 */

namespace DuckDev\Redirect\Views;

use DuckDev\Redirect\Utils\Abstracts\View;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Pages extends View {

	/**
	 * Register the menu for the error logs page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function logs() {
		echo '<div id="dd-404-to-301-logs"></div>';

		/**
		 * Action hook to run something after rendering logs page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_logs_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function settings() {
		$tabs    = $this->get_tabs();
		$current = $this->get_current_tab();

		// Admin settings template.
		include_once 'templates/admin-settings.php';

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_settings_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public function get_tabs() {
		$tabs = array(
			'redirect'      => array(
				'label' => __( 'Redirect', '404-to-301' ),
				'icon'  => 'redo',
			),
			'logs'          => array(
				'label' => __( 'Logs', '404-to-301' ),
				'icon'  => 'database',
			),
			'notifications' => array(
				'label' => __( 'Notifications', '404-to-301' ),
				'icon'  => 'email',
			),
			'general'       => array(
				'label' => __( 'General', '404-to-301' ),
				'icon'  => 'admin-generic',
			),
		);

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_admin_settings_page_get_tabs', $tabs );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @param string $default Default tab.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public function get_current_tab( $default = 'redirect' ) {
		// Get tab value.
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );

		// Get allowed tabs.
		$tabs = array_keys( $this->get_tabs() );

		// Make sure it's not empty.
		$tab = empty( $tab ) || ! in_array( $tab, $tabs, true ) ? $default : $tab;

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_admin_settings_page_get_current_tab', $tab );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @param string $tab Default tab.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public function get_current_class( $tab = 'redirect' ) {
		if ( $this->get_current_tab() === $tab ) {
			$class = 'bg-gray-50 text-wpblue-700 hover:text-wpblue-700 hover:bg-white group rounded-md px-3 py-2 flex items-center text-sm font-medium';
		} else {
			$class = 'text-gray-900 hover:text-gray-900 hover:bg-gray-50 group rounded-md px-3 py-2 flex items-center text-sm font-medium';
		}

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_admin_settings_page_get_current_class', $class );
	}
}
