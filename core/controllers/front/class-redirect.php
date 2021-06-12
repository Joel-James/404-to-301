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

namespace DuckDev\Redirect\Controllers\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Controller;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Redirect extends Controller {

	/**
	 * Holds the slug of the plugin admin main menu.
	 *
	 * @since  4.0.0
	 * @var    string
	 */
	const SLUG = '404-to-301';

	/**
	 * Initialize the menu class and register the hooks.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		//add_action( 'admin_menu', array( $this, 'admin_menu' ) );
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
			self::SLUG,
			null,
			'dashicons-redo',
			89
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
			'manage_options', // Menu permission.
			'404-to-301-settings',
			null
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
