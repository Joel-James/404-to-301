<?php
/**
 * The plugin information class.
 *
 * This class contains the properties of the plugin and the basic
 * information you need about the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Plugin
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Plugin.
 *
 * @since    4.0.0
 * @package  DuckDev\Redirect
 */
class Plugin {

	/**
	 * Holds the slug of the plugin.
	 *
	 * To access this property, you should use Plugin::slug().
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access private
	 */
	private static $slug = '404-to-301';

	/**
	 * Holds the name of the plugin.
	 *
	 * To access this property, you should use Plugin::name().
	 * This can not be translated.
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access private
	 */
	private static $name = '404 to 301';

	/**
	 * Holds the screen IDs of our plugin pages.
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
	 * Get the plugin slug name.
	 *
	 * This slug should be same as wp.org slug.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function slug() {
		return self::$slug;
	}

	/**
	 * Get the plugin name string.
	 *
	 * This should not be translated.
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
	 * Get the plugin version number.
	 *
	 * You can also use DD4T3_VERSION constant value directly.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function version() {
		return DD4T3_VERSION;
	}

	/**
	 * Get the plugin name admin screens.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string[]
	 */
	public static function screens() {
		return self::$pages;
	}

	/**
	 * Getter method to get the plugin admin pages.
	 *
	 * Admin page id and url will be keyed with short name as key.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string[]
	 */
	public static function pages() {
		$pages = array();

		foreach ( self::screens() as $key => $id ) {
			$pages[ $key ] = array(
				'id'  => $id,
				'url' => admin_url( 'admin.php?page=404-to-301-' . $key ),
			);
		}

		return $pages;
	}

	/**
	 * Get URL of one of our plugin pages.
	 *
	 * @param string $page Page key.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_url( $page = 'settings' ) {
		$pages = self::pages();

		return isset( $pages[ $page ]['url'] ) ? $pages[ $page ]['url'] : '';
	}

	/**
	 * Run plugin activation hooks.
	 *
	 * Make sure to call this in register_activation_hook().
	 * Otherwise, it won't work.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public static function activate() {
		// Setup database.
		Database\Database::instance();

		/**
		 * Action hook to run after activating plugin.
		 *
		 * This won't work inside our plugin because our plugin
		 * will boot only after the activation hook is fired.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_activated' );
	}

	/**
	 * Run plugin deactivation hooks.
	 *
	 * Make sure to call this in register_deactivation_hook().
	 * Otherwise, it won't work.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public static function deactivate() {
		/**
		 * Deactivation hook to run after activating plugin.
		 *
		 * This won't work inside our plugin because our plugin
		 * will boot only after the activation hook is fired.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_deactivated' );
	}
}
