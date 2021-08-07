<?php
/**
 * The plugin base class.
 *
 * This class contains the properties of the plugin and the basic
 * information you need about the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @subpackage Core
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Creates a new object template.
 *
 * @since  5.0.0
 * @access public
 */
class Plugin {

	/**
	 * Holds the name of the plugin in class format.
	 *
	 * To access this property, you should use the name().
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access private
	 */
	private static $slug = '404-to-301';

	/**
	 * Holds the name of the plugin in class format.
	 *
	 * To access this property, you should use the name().
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access private
	 */
	private static $name = '404 to 301';

	/**
	 * Holds the screen IDs of plugin pages.
	 *
	 * @var string[]
	 * @since  4.0.0
	 * @access private
	 */
	private static $pages = array(
		'logs'      => 'toplevel_page_404-to-301-logs',
		'settings'  => 'logs_page_404-to-301-settings',
		'redirects' => 'logs_page_404-to-301-redirects',
	);

	/**
	 * Getter method to get the plugin name.
	 *
	 * @since  5.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function slug() {
		return self::$slug;
	}

	/**
	 * Getter method to get the plugin name.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function name() {
		return self::$name;
	}

	/**
	 * Getter method to get the plugin admin page IDs.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string[]
	 */
	public static function pages() {
		$pages = array();

		foreach ( self::$pages as $key => $id ) {
			$pages[ $key ] = array(
				'id'  => $id,
				'url' => admin_url( 'admin.php?page=404-to-301-' . $key ),
			);
		}

		return $pages;
	}

	/**
	 * Run plugin activation hooks.
	 *
	 * Make sure to call this in register_activation_hook().
	 * Otherwise, it won't work.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function activate() {
		// Setup database.
		Database\DB::instance();

		/**
		 * Action hook to run after activating plugin.
		 *
		 * This won't work inside our plugin because our plugin
		 * will boot only after the activation hook is fired.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_activated' );
	}

	/**
	 * Run plugin deactivation hooks.
	 *
	 * Make sure to call this in register_deactivation_hook().
	 * Otherwise, it won't work.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function deactivate() {
		/**
		 * Deactivation hook to run after activating plugin.
		 *
		 * This won't work inside our plugin because our plugin
		 * will boot only after the activation hook is fired.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_deactivated' );
	}
}
