<?php

namespace DuckDev\WP404\Controllers\Admin;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;
use DuckDev\WP404\Views\Admin\Pages;

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
	 * @var string
	 * @since 4.0
	 *
	 */
	private $slug = DD404_SLUG;

	/**
	 * Initilize the class by registering the hooks.
	 *
	 * @since 4.0.0
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_menu', [ $this, 'rename_menu' ] );
	}

	/**
	 * Register the menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function admin_menu() {
		// Error logs main menu.
		$this->logs();

		// Settings sub menu.
		$this->settings();
	}

	/**
	 * Rename admin menu text to : 404 to 301.
	 *
	 * @since  4.0
	 * @global array $menu Menus registered in this site.
	 *
	 * @return void
	 */
	public function rename_menu() {
		global $menu;

		// Rename the menu name.
		$menu[90][0] = __( '404 to 301', '404-to-301' );
	}

	/**
	 * Register the menu for the error logs page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function logs() {
		// Main logs page.
		$page_hook = add_menu_page(
			__( '404 Error Logs', '404-to-301' ),
			__( 'Error Logs', '404-to-301' ),
			'manage_options', // Menu permission.
			$this->slug,
			[ Pages::get(), 'logs' ],
			'dashicons-redo',
			90
		);

		/**
		 * Action hook to run something when we are on logs page.
		 *
		 * This hook can be used to add new settings menu items.
		 *
		 * @param string $page_hook Menu string.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_admin_menu_logs', $page_hook );
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
			'manage_options', // Menu permission.
			'404-to-301-settings',
			[ Pages::get(), 'settings' ]
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
		do_action( 'dd404_admin_menu_settings', $page_hook );
	}
}
