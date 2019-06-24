<?php
/**
 * Plugin Name:     404 to 301
 * Plugin URI:      https://duckdev.com/
 * Description:     The boilerplate plugin for WordPress plugin development.
 * Version:         4.0.0
 * Author:          Joel James
 * Author URI:      https://duckdev.com/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:     dd-boilerplate
 * Domain Path:     /languages
 *
 * Copyright 2017-2018 Duck Dev (http://duckdev.com)
 *
 * 404 to 301 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * 404 to 301 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with 404 to 301. If not, see <http://www.gnu.org/licenses/>.
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

// Define DD404_PLUGIN_FILE.
define( 'DD404_PLUGIN_FILE', __FILE__ );

// Auto load classes.
require_once dirname( __FILE__ ) . '/core/vendor/autoloader.php';

/**
 * Main instance of plugin.
 *
 * Returns the main instance of 404 to 301 to prevent the need to use globals
 * and to maintain a single copy of the plugin object.
 * You can simply call duckdev_404() to access the object.
 *
 * @since  4.0.0
 *
 * @return DuckDev404\Core\Main
 */
function duckdev_404() {
	return DuckDev404\Core\Main::get();
}

// Check the minimum required PHP version (5.6) and run the plugin.
if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	// Run the plugin.
	duckdev_404();

	// Activation hook.
	register_activation_hook( DD404_PLUGIN_FILE, array( duckdev_404(), 'activate' ) );
} else {
	// Show an admin notice.
	add_action( 'admin_notices', function() {
		printf(
			__( '%1$sUh oh! %2$s requires a minimum PHP version of 5.6. WordPress core also %3$sbumps minimum required PHP version to 5.6.%4$s', '404-to-301' ),
			'<div class="notice notice-error"><p>',
			'<strong>404 to 301</strong>',
			'<a href="https://make.wordpress.org/core/2018/12/08/updating-the-minimum-php-version/" target="_blank">',
			'</a></p></div>'
		);
	} );
}
