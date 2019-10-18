<?php
/**
 * Plugin Name:     404 to 301 - Redirect, Log and Notify 404 Errors
 * Plugin URI:      https://duckdev.com/
 * Description:     Automatically redirect all <strong>404 errors</strong> to any page using <strong>301 redirect for SEO</strong>. You can <strong>redirect and log</strong> every 404 errors. No more 404 errors in Webmaster tool.
 * Version:         4.0.0
 * Author:          Joel James
 * Author URI:      https://duckdev.com/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:     404-to-301
 * Domain Path:     /languages
 *
 * Copyright 2017-2018 Duck Dev (http://duckdev.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// K. Bye.
defined( 'WPINC' ) || die;

// Define plugin version.
define( 'DD404_VERSION', '4.0.0' );

// Define plugin file.
define( 'DD404_PLUGIN_FILE', __FILE__ );

// Define plugin directory path.
define( 'DD404_DIR', plugin_dir_path( __FILE__ ) );

// Define plugin directory url.
define( 'DD404_URL', plugin_dir_url( __FILE__ ) );

// Define plugin slug name.
define( 'DD404_SLUG', '404-to-301' );

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
 * @return DuckDev\WP404\Plugin
 */
function duckdev_404_to_301() {
	return DuckDev\WP404\Plugin::_get();
}

// Check the minimum required PHP version (5.6) and run the plugin.
if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	// Activation hook.
	register_activation_hook( DD404_PLUGIN_FILE, array( duckdev_404_to_301(), 'activate' ) );

	// Run the plugin.
	add_action( 'plugins_loaded', 'duckdev_404_to_301' );
}
