<?php
/**
 * Plugin Name:     404 to 301 - Redirect, Log and Notify 404 Errors
 * Plugin URI:      https://duckdev.com/products/404-to-301/
 * Description:     Automatically redirect all <strong>404 errors</strong> to any page using <strong>301 redirect for SEO</strong>. You can <strong>redirect and log</strong> every 404 errors. No more 404 errors in Webmaster tool.
 * Version:         3.1.3
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
 * @author   Joel James <mail@cjoel.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @category Core
 * @link     https://duckdev.com/products/404-to-301/
 * @package  JJ4T3
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

// Define plugin slug name.
define( 'JJ4T3_NAME', '404-to-301' );
// Define plugin directory.
define( 'JJ4T3_DIR', plugin_dir_path( __FILE__ ) );
// Define plugin base url.
define( 'JJ4T3_URL', plugin_dir_url( __FILE__ ) );
// Define plugin base file.
define( 'JJ4T3_BASE_FILE', __FILE__ );
// Define plugin version.
define( 'JJ4T3_VERSION', '3.1.3' );
// Define plugin version.
define( 'JJ4T3_DB_VERSION', '11.0' );
// Define plugin log table.
define( 'JJ4T3_TABLE', $GLOBALS['wpdb']->prefix . '404_to_301' );

// Set who all can access plugin settings.
// You can change this if you want to give others access.
if ( ! defined( 'JJ4T3_ACCESS' ) ) {
	define( 'JJ4T3_ACCESS', 'manage_options' );
}

// File that contains main plugin class.
require_once JJ4T3_DIR . 'includes/class-jj-404-to-301.php';
require_once JJ4T3_DIR . 'includes/class-jj4t3-activator-deactivator-uninstaller.php';

/**
 * The main function for that returns JJ_404_to_301
 *
 * The main function responsible for returning the one true JJ_404_to_301
 * instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $jj4t3 = jj_404_to_301(); ?>
 *
 * @since 3.0.0
 *
 * @return JJ_404_to_301|object
 */
function jj_404_to_301() {
	return JJ_404_to_301::instance();
}

/**
 * Plugin activation actions.
 *
 * Actions to perform during plugin activation.
 * We will be registering default options in this function.
 *
 * @uses   register_activation_hook() To register activation hook.
 */
register_activation_hook(
	JJ4T3_BASE_FILE,
	array( 'JJ4T3_Activator_Deactivator_Uninstaller', 'activate' )
);

// Check the minimum required PHP version (5.6) and run the plugin.
if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	// Run the plugin.
	add_action( 'plugins_loaded', 'jj_404_to_301' );
}
