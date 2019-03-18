<?php

namespace DuckDev404\Inc\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Core\Base;

/**
 * The menu-specific functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Menu extends Base {

	/**
	 * Menu slug for admin page.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	private $slug = 'dd404-logs';

	/**
	 * Page class instance.
	 *
	 * @since 4.0
	 *
	 * @var Page
	 */
	private $page;

	/**
	 * Initialize the class by registering hooks.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function init() {
		// Page class instance.
		$this->page = Page::instance();

		// Setup our menu.
		$this->add_action( 'admin_menu', 'admin_menu' );
	}

	/**
	 * Register the menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function admin_menu() {
		global $menu;

		// Main logs page.
		add_menu_page(
			__( '404 Error Logs', '404-to-301' ),
			__( '404 Errors', '404-to-301' ),
			DD404_ACCESS, // Menu permission.
			$this->slug,
			array( $this->page, 'logs' ),
			'dashicons-redo',
			90
		);

		// Rename the menu name.
		$menu[90][0] = __( '404 to 301', '404-to-301' );

		// Example sub menu.
		$this->settings();
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function settings() {
		// Sub page.
		$page_hook = add_submenu_page(
			$this->slug,
			__( '404 to 301 Settings', '404-to-301' ),
			__( 'Settings', '404-to-301' ),
			DD404_ACCESS, // Menu permission.
			'dd404-settings',
			array( $this->page, 'settings' )
		);

		/**
		 * Action hook to run something when we are on settings page.
		 *
		 * This hook can be used to add new settings menu items.
		 *
		 * @param string $page_hook Menu string.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_menu_settings_sub_menu', $page_hook );
	}
}
