<?php
/**
 * Plugin Name:     404 to 301
 * Plugin URI:      https://duckdev.com/products/404-to-301/
 * Description:     Automatically redirect all <strong>404 errors</strong> to any page using <strong>301 redirect for SEO</strong>. You can <strong>redirect and log</strong> every 404 errors. No more 404 errors in Webmaster tool.
 * Version:         3.0.4
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
 * @category Core
 * @package  JJ4T3
 * @author   Joel James <mail@cjoel.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://duckdev.com/products/404-to-301/
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

// Stay lazy if our class is already there.
if ( ! class_exists( 'JJ_404_to_301' ) ) :

	/**
	 * File that contains main plugin class.
	 */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jj-404-to-301.php';

	/**
	 * Setup plugin constants.
	 *
	 * We need a few constants in our plugin.
	 * These values should be constant and con't
	 * be altered later.
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return void
	 */
	function jj4t3_set_constants() {

		$constants = array(
			'JJ4T3_NAME'       => '404-to-301',
			'JJ4T3_DOMAIN'     => '404-to-301',
			'JJ4T3_DIR'        => plugin_dir_path( __FILE__ ),
			'JJ4T3_URL'        => plugin_dir_url( __FILE__ ),
			'JJ4T3_BASE_FILE'  => __FILE__,
			'JJ4T3_VERSION'    => '3.0.4',
			'JJ4T3_DB_VERSION' => '11.0',
			'JJ4T3_TABLE'      => $GLOBALS['wpdb']->prefix . '404_to_301',
			// Set who all can access plugin settings.
			// You can change this if you want to give others access.
			'JJ4T3_ACCESS'     => 'manage_options',
		);

		foreach ( $constants as $constant => $value ) {
			if ( ! defined( $constant ) ) {
				define( $constant, $value );
			}
		}
	}

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
	 * @return JJ_404_to_301|object
	 */
	function JJ_404_to_301() {

		return JJ_404_to_301::instance();
	}

	/**
	 * Create a helper function for easy SDK access.
	 *
	 * This function is used to integrate Freemius SDK to 404 to 301 plugin
	 * for addons, support and analytics (if allowed).
	 *
	 * @since 3.0.0
	 *
	 * @return Freemius
	 */
	function jj4t3_freemius() {

		global $jj4t3_fs;

		// If freemius is already initialized.
		if ( ! isset( $jj4t3_fs ) ) {

			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';

			// Initialize freemius sdk.
			$jj4t3_fs = fs_dynamic_init( array(
				'id'               => '2192',
				'slug'             => '404-to-301',
				'type'             => 'plugin',
				'public_key'       => 'pk_9d470f3128e5e491ea5a2da6bf4bf',
				'is_premium'       => false,
				'has_addons'       => true,
				'has_paid_plans'   => false,
				'anonymous_mode'   => true, // Temporary fix.
				'menu'             => array(
					'slug'    => 'jj4t3-logs',
					'account' => false,
					'support' => false,
					'contact' => false,
				),
			) );
		}

		return $jj4t3_fs;
	}

	// Set constants.
	jj4t3_set_constants();

	// Init Freemius.
	jj4t3_freemius();

	// Init 404 to 301.
	JJ_404_to_301();

	// Uninstaller for 404 to 301.
	jj4t3_freemius()->add_action( 'after_uninstall', array(
		'JJ4T3_Activator_Deactivator_Uninstaller',
		'uninstall'
	) );

	// Signal that SDK was initiated.
	do_action( 'jj4t3_fs_loaded' );

endif; // End if class_exists check.