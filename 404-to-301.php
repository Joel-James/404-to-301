<?php
/**
 * Plugin Name:     404 to 301
 * Plugin URI:      https://thefoxe.com/products/404-to-301/
 * Description:     Automatically redirect all <strong>404 errors</strong> to any page using <strong>301 redirect for SEO</strong>. You can <strong>redirect and log</strong> every 404 errors. No more 404 errors in Webmaster tool.
 * Version:         2.2.8
 * Author:          Joel James
 * Author URI:      https://thefoxe.com/
 * Donate link:     https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XUVWY8HUBUXY4
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
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
 * @package  I4T3
 * @author   Joel James <me@joelsays.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://thefoxe.com/products/404-to-301
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die( 'Damn it.! Dude you are looking for what?' );
}

if ( ! class_exists( '_404_To_301' ) ) {
    
    // Constants array
    $constants = array(
        'I4T3_NAME' => '404-to-301',
        'I4T3_DOMAIN' => '404-to-301',
        'I4T3_PATH' => plugins_url( '/404-to-301/' ),
        'I4T3_PLUGIN_DIR' => dirname(__FILE__),
        'I4T3_BASE' => __FILE__,
        'I4T3_SETTINGS_PAGE' => admin_url( 'admin.php?page=i4t3-settings' ),
        'I4T3_HELP_PAGE' => admin_url( 'admin.php?page=i4t3-settings&tab=credits' ),
        'I4T3_LOGS_PAGE' => admin_url( 'admin.php?page=i4t3-logs' ),
        'I4T3_DB_VERSION' => '8',
        'I4T3_VERSION' => '2.2.8',
        'I4T3_TABLE' => $GLOBALS['wpdb']->prefix . '404_to_301',
        // Set who all can access 404 settings.
        // You can change this if you want to give others access.
        'I4T3_ADMIN_PERMISSION' => 'manage_options'
    );

    foreach ($constants as $constant => $value) {
        // Define constants if not defined already
        if ( ! defined( $constant ) ) {
            define( $constant, $value );
        }
    }

    /**
     * The function that runs during plugin activation.
     * 
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    function activate_i4t3() {
        
        include_once I4T3_PLUGIN_DIR . '/includes/class-404-to-301-activator.php';
        
        _404_To_301_Activator::activate();
    }
    
    // plugin activation hook
    register_activation_hook(__FILE__, 'activate_i4t3');

    /**
     * The core plugin class that is used to define
     * dashboard-specific hooks, and public-facing site hooks.
     */
    require_once plugin_dir_path(__FILE__) . 'includes/class-404-to-301.php';

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    function run_i4t3() {

        $plugin = new _404_To_301();
        $plugin->run();
    }

    run_i4t3();

}

//*** Thank you for your interest in 404 to 301 - Developed and managed by Joel James ***// 