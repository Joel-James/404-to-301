<?php
/**
 * The plugin admin menu class.
 *
 * This class handles the admin menu functionality for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Admin
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
 * @package DuckDev\Redirect\Admin
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

		// Error logs main menu.
		$this->redirects();

		// Settings sub menu.
		$this->settings();

		/**
		 * Action hook to run after setup all admin menus for plugin.
		 *
		 * Other plugins can use this hook to add new sub menu items
		 * to the main 404 to 301 menu.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_admin_menu' );
	}

	/**
	 * Rename admin menu text to : 404 to 301.
	 *
	 * This is to make sure the main label is plugin's name.
	 *
	 * @global array $menu Menus registered in this site.
	 * @since  4.0.0
	 *
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
	 * This is where the 404 error logs are listed.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function logs() {
		// Main logs page.
		add_menu_page(
			__( '404 Error Logs', '404-to-301' ),
			__( 'Logs', '404-to-301' ),
			Permission::get_cap(),
			self::SLUG,
			array( Views\Logs::instance(), 'content' ),
			'dashicons-redo',
			89
		);
	}

	/**
	 * Register the menu for the redirects list.
	 *
	 * This is where the custom redirects for 404s listed.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function redirects() {
		// Redirects page.
		add_submenu_page(
			self::SLUG,
			__( '404 to 301 Custom Redirects', '404-to-301' ),
			__( 'Redirects', '404-to-301' ),
			Permission::get_cap(),
			'404-to-301-redirects',
			array( Views\Redirects::instance(), 'content' )
		);
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * This is where the plugin settings are handled.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function settings() {
		// Settings sub page.
		add_submenu_page(
			self::SLUG,
			__( '404 to 301 Settings', '404-to-301' ),
			__( 'Settings', '404-to-301' ),
			Permission::get_cap(),
			'404-to-301-settings',
			array( Views\Settings::instance(), 'base_content' )
		);
	}
}
