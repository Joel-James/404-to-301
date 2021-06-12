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
class Settings extends View {

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function content() {
		// Arguments.
		$args = array(
			'menu_config' => array(
				'current' => $this->get_current_tab(),
				'items'   => $this->get_settings_tabs(),
			),
		);

		// Admin settings template.
		$this->render( 'settings', $args );

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
	public function get_settings_tabs() {
		$tabs = array(
			'redirect'      => array(
				'title' => __( 'Redirect', '404-to-301' ),
				'icon'  => 'redirect',
				'url'   => add_query_arg( 'tab', 'redirect' ),
			),
			'logs'          => array(
				'title' => __( 'Logs', '404-to-301' ),
				'icon'  => 'logs',
				'url'   => add_query_arg( 'tab', 'logs' ),
			),
			'notifications' => array(
				'title' => __( 'Notifications', '404-to-301' ),
				'icon'  => 'email',
				'url'   => add_query_arg( 'tab', 'notifications' ),
			),
			'general'       => array(
				'title' => __( 'General', '404-to-301' ),
				'icon'  => 'settings',
				'url'   => add_query_arg( 'tab', 'general' ),
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
	public function get_current_tab( $default = 'general' ) {
		// Get tab value.
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );

		// Get allowed tabs.
		$tabs = array_keys( $this->get_settings_tabs() );

		// Make sure it's not empty.
		$tab = empty( $tab ) || ! in_array( $tab, $tabs, true ) ? $default : $tab;

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_admin_settings_page_get_current_tab', $tab );
	}
}
