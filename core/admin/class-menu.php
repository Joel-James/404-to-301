<?php
/**
 * The plugin menu controller class.
 *
 * This class handles the admin menu functionality for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Menu
 */

namespace DuckDev\Redirect\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Views;
use DuckDev\Redirect\Plugin;
use DuckDev\Redirect\Permission;
use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Menu extends Base {

	/**
	 * Holds the slug of the plugin admin main menu.
	 *
	 * @var string
	 *
	 * @since  4.0.0
	 */
	const SLUG = '404-to-301-logs';

	/**
	 * Initialize the menu class and register the hooks.
	 *
	 * @since  4.0.0
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'rename_menu' ) );
	}

	/**
	 * Register the menu for the admin area of the plugin.
	 *
	 * This method should handle all the submenus that the plugin
	 * needs also.
	 *
	 * @since  4.0.0
	 * @access public
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
	 * Register the menu for the admin area of the plugin.
	 *
	 * This method should handle all the submenus that the plugin
	 * needs also.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function is_settings() {
		// Error logs main menu.
		$this->logs();

		// Settings sub menu.
		$this->settings();
	}

	/**
	 * Rename admin menu text to : 404 to 301.
	 *
	 * @global array $menu Menus registered in this site.
	 *
	 * @since  4.0
	 * @return void
	 */
	public function rename_menu() {
		global $menu;

		foreach ( $menu as $position => $data ) {
			// Only when it's our menu.
			if ( isset( $data[2] ) && self::SLUG === $data[2] ) {
				// Rename the plugin main menu title.
				// phpcs:ignore
				$menu[ $position ][0] = Plugin::instance()->name();
			}
		}
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
			__( 'Logs', '404-to-301' ),
			Permission::settings_cap(),
			self::SLUG,
			array( Views\Logs::instance(), 'content' ),
			'dashicons-redo',
			89
		);

		$page_hook = add_submenu_page(
			self::SLUG,
			__( '404 to 301 Settings', '404-to-301' ),
			__( 'Redirects', '404-to-301' ),
			Permission::settings_cap(),
			'404-to-301-redirects',
			array( Views\Redirects::instance(), 'content' )
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
			self::SLUG,
			__( '404 to 301 Settings', '404-to-301' ),
			__( 'Settings', '404-to-301' ),
			Permission::settings_cap(),
			'404-to-301-settings',
			array( Views\Settings::instance(), 'base_content' )
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
