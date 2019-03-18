<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link            https://duckdev.com
 * @since           4.0
 * @package         404-to-301
 *
 * @wordpress-plugin
 * Plugin Name:     404 to 301
 * Plugin URI:      https://duckdev.com/
 * Description:     The boilerplate plugin for WordPress plugin development.
 * Version:         4.0
 * Author:          Joel James
 * Author URI:      https://duckdev.com/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:     dd-boilerplate
 * Domain Path:     /languages
 *
 * Copyright 2017-2018 Duck Dev (http://duckdev.com)
 * Author - Joel James
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
require_once dirname( __FILE__ ) . '/inc/vendor/autoloader.php';

/**
 * Main instance of Main.
 *
 * Returns the main instance of DD Boilerplate to prevent the need to use globals
 * and to maintain a single copy of the plugin app object.
 *
 * @since  4.0
 *
 * @return \DuckDev404\Inc\Main
 */
function duckdev_404() {
	return DuckDev404\Inc\Main::instance();
}

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	duckdev_404();
}
