<?php

namespace DuckDev404\Inc\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Core\Base;
use DuckDev404\Inc\Helpers;


/**
 * The page-specific functionality of the plugin.
 *
 * Loading page specific views are handled in this class.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Page extends Base {

	/**
	 * Register the error logs page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function logs() {
		// Get args for the page.
		$args = array();

		// Error logs page content.
		Helpers\General::view( 'admin/common/header' );
		Helpers\General::view( 'admin/logs', $args );
		Helpers\General::view( 'admin/common/footer' );
	}

	/**
	 * Register plugin settings page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function settings() {
		$tabs = array(
			'default' => __( 'Settings', '404-to-301' ),
		);

		/**
		 * Filter to add/remove menu items in settings page.
		 *
		 * @param array $menu_items Menu items.
		 *
		 * @since 4.0
		 */
		$tabs = apply_filters( 'dd404_settings_menu_items', $tabs );

		// Get args for the page.
		$args = array(
			'tab'  => Helpers\Menu::current_tab( 'sub_page' ),
			'tabs' => $tabs,
		);

		Helpers\General::view( 'admin/common/header' );
		Helpers\General::view( 'admin/settings', $args );
		Helpers\General::view( 'admin/common/footer' );
	}
}