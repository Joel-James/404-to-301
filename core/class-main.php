<?php

namespace DuckDev404\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Core\Utils\Abstracts\Base;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
final class Main extends Base {

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
	protected function __construct() {
		// Setup required constants.
		$this->setup_constants();

		// Run activation scripts.
		$this->activate();

		// Run the plugin.
		$this->run();
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
	private function setup_constants() {
		// Plugin name.
		define( 'DD404_NAME', '404-to-301' );
		// Plugin version.
		define( 'DD404_VERSION', '4.0' );
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
	public function activate() {
		// The code that runs during plugin activation.
	}

	/**
	 * Register all the hooks.
	 *
	 * Register all actions and filers with WordPress.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	public function run() {
		Hooks::get()->setup();
	}
}
