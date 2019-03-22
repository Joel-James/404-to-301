<?php

namespace DuckDev404\Inc\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 **/
class Activator {

	/**
	 * Function that runs during plugin activation.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public static function activate() {
		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'This plugin requires a minimum PHP Version of 5.6', '404-to-301' ) );
		}

		// Create tables.
		self::create_tables();
	}

	/**
	 * Create custom tables for DD Boilerplate.
	 *
	 * Use dbDelta function to upgrade or create tables
	 * safely without breaking anything.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private static function create_tables() {
		// Create custom tables required for this plugin.
	}
}
