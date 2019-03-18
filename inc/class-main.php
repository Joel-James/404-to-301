<?php

namespace DuckDev404\Inc;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Core;
use DuckDev404\Inc\Admin;
use DuckDev404\Inc\Front;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
final class Main extends Core\Base {

	/**
	 * Admin class instance.
	 *
	 * @var Admin\Admin
	 *
	 * @since  4.0
	 * @access private
	 */
	private $admin;

	/**
	 * Front class instance.
	 *
	 * @var Front\Front
	 *
	 * @since  4.0
	 * @access private
	 */
	private $front;

	/**
	 * Core class instance.
	 *
	 * @var Core\Core
	 *
	 * @since  4.0
	 * @access private
	 */
	private $core;

	/**
	 * Initialize functionality of the plugin.
	 *
	 * This is where we kick-start the plugin by defining
	 * everything required and register all hooks.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return void
	 */
	protected function init() {
		$this->define_constants();
		$this->set_locale();
		$this->init_hooks();
		$this->define_classes();
	}

	/**
	 * Getter method for core.
	 *
	 * @since 4.0
	 *
	 * @return Core\Core
	 */
	public function core() {
		return $this->core;
	}

	/**
	 * Getter method for admin.
	 *
	 * @since 4.0
	 *
	 * @return Admin\Admin
	 */
	public function admin() {
		return $this->admin;
	}

	/**
	 * Getter method for front.
	 *
	 * @since 4.0
	 *
	 * @return Front\Front
	 */
	public function front() {
		return $this->front;
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_locale() {
		Core\I18n::instance();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * Note: Do not write any heavy tasks in __constructor of classes.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function define_classes() {
		// Define controllers.
		$this->admin = Admin\Admin::instance();
		$this->front = Front\Front::instance();
		$this->core  = Core\Core::instance();
	}

	/**
	 * Define DD Boilerplate constants.
	 *
	 * These constants can not be changed.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function define_constants() {
		// Plugin name.
		define( 'DD404_NAME', '404-to-301' );
		// Plugin version.
		define( 'DD404_VERSION', '4.0' );
		// Shared UI version.
		define( 'DD404_UI_VERSION', 'sui-2-3-9' );
		// Plugin directory.
		define( 'DD404_DIR', plugin_dir_path( DD404_PLUGIN_FILE ) );
		// Plugin url.
		define( 'DD404_URL', plugin_dir_url( DD404_PLUGIN_FILE ) );
		// Plugin base file.
		define( 'DD404_BASENAME', plugin_basename( DD404_PLUGIN_FILE ) );
		// Plugin vendor directory.
		define( 'DD404_VENDOR', DD404_DIR . 'inc/vendor/' );

		// Optional constants.
		if ( ! defined( 'DD404_ACCESS' ) ) {
			define( 'DD404_ACCESS', 'manage_options' );
		}
	}

	/**
	 * Initialize the hooks.
	 *
	 * Set activation and deactivation hooks here.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {
		// The code that runs during plugin activation.
		register_activation_hook( DD404_PLUGIN_FILE, array( 'DuckDev404\Inc\Core\Activator', 'activate' ) );
	}
}
