<?php
/**
 * Plugin Name:     404 to 301 - Redirect, Log and Notify 404 Errors
 * Plugin URI:      https://duckdev.com/products/404-to-301/
 * Description:     Automatically redirect all <strong>404 errors</strong> to any page using <strong>301 redirect for SEO</strong>. You can <strong>redirect and log</strong> every 404 errors. No more 404 errors in Webmaster tool.
 * Version:         4.0.0-beta
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
 * @link     https://duckdev.com/products/404-to-301/
 * @author   Joel James <me@joelsays.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @category Core
 * @package  DD4T3
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

// Minimum PHP version is 5.6.
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	/**
	 * Show a notice if plugin can not be run.
	 *
	 * This plugin requires minimum PHP version of 5.6. If the current
	 * server does not meet the requirement, show an admin notice and bail.
	 *
	 * @since 4.0.0
	 */
	add_action(
		'admin_notices',
		function () { ?>
			<div class="notice notice-error">
				<p>
					<?php
					printf(
						// translators: 1: plugin name. 2: minimum PHP version. 3: current PHP version.
						esc_attr__( 'The %1$s plugin cannot run on PHP versions older than %2$s. Your current version is %3$s. %4$sPlease upgrade%5$s.', '404-to-301' ),
						'<strong>404 to 301</strong>',
						'<strong>5.6</strong>',
						'<strong>' . PHP_VERSION . '</strong>',
						'<a href="https://wordpress.org/support/update-php/" target="_blank">',
						'</a>'
					);
					?>
				</p>
			</div>
			<?php
		}
	);

	return; // Exit plugin.
}

// Plugin version.
define( 'REDIRECTPRESS_VERSION', '4.0.0-beta' );

// Plugin database version.
define( 'REDIRECTPRESS_DB_VERSION', '4.0.0' );

// Plugin url.
define( 'REDIRECTPRESS_URL', plugin_dir_url( __FILE__ ) );

// Plugin directory path.
define( 'REDIRECTPRESS_DIR', plugin_dir_path( __FILE__ ) );

// Plugin file.
define( 'REDIRECTPRESS_FILE', __FILE__ );

// Plugin base name.
define( 'REDIRECTPRESS_BASE_NAME', plugin_basename( __FILE__ ) );

// Auto load classes.
require_once REDIRECTPRESS_DIR . '/vendor/autoload.php';

// Activation actions.
register_activation_hook( __FILE__, array( 'RedirectPress\Plugin', 'activate' ) );

// Deactivation actions.
register_deactivation_hook( __FILE__, array( 'RedirectPress\Plugin', 'deactivate' ) );

/**
 * The main RedirectPress\Core instance.
 *
 * Use this function like you would a global variable, except
 * without needing to declare the global.
 * We will initialize only if required WP and PHP versions match.
 *
 * Example: $redirectpress = \RedirectPress\Core::instance();
 *
 * @since 4.0.0
 *
 * @return RedirectPress\Core
 */
add_action( 'plugins_loaded', array( 'RedirectPress\Core', 'instance' ) );
