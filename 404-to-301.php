<?php
/**
 * Plugin Name:     404 to 301 - Redirect, Log and Notify 404 Errors
 * Plugin URI:      https://duckdev.com/products/404-to-301/
 * Description:     Automatically redirect all <strong>404 errors</strong> to any page using <strong>301 redirect for SEO</strong>. You can <strong>redirect and log</strong> every 404 errors. No more 404 errors in Webmaster tool.
 * Version:         4.0.0
 * Author:          Joel James
 * Author URI:      https://duckdev.com/
 * Donate link:     https://paypal.me/JoelCJ
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:     404-to-301
 * Domain Path:     /languages
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
 *
 * @author   Joel James <me@joelsays.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @category Core
 * @link     https://duckdev.com/products/404-to-301/
 * @package  DD4T3
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

// Plugin version.
define( 'DD4T3_VERSION', '4.0.0' );

// Plugin path.
define( 'DD4T3_URL', plugin_dir_url( __FILE__ ) );

// Plugin URL.
define( 'DD4T3_DIR', plugin_dir_path( __FILE__ ) );

// Auto load classes.
require_once DD4T3_DIR . '/vendor/autoload.php';

/**
 * The main function for that returns JJ_404_to_301
 *
 * The main function responsible for returning the one true JJ_404_to_301
 * instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $jj4t3 = JJ_404_to_301(); ?>
 *
 * @since 3.0.0
 *
 * @return DuckDev\DD4T3\Core
 */
function duckdev_404_to_301() {
	// Get the plugin instance.
	$instance = DuckDev\DD4T3\Core::instance();

	/**
	 * Action hook to execute after plugin is loaded.
	 *
	 * Addon plugins can use this to initialize.
	 *
	 * @param DuckDev\DD4T3\Core $instance Plugin instance.
	 *
	 * @since 4.0.0
	 */
	do_action( '404_to_301_init', $instance );

	return $instance;
}

// Load our plugin if minimum version is PHP 5.6.
if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
	// Use the plugins_loaded hook to setup the plugin instance.
	add_action( 'plugin_loaded', 'duckdev_404_to_301' );
}
