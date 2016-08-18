<?php

if (!defined('WPINC')) {
    die('Damn it.! Dude you are looking for what?');
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage Activator
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */
class _404_To_301_Activator {

    /**
     * Function to run during activation
     * 
     * We register default options to the WordPress
     * if not exists already.
     *
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public static function activate() {

        // default settings array
        $options = array(
            'redirect_type' => '301',
            'redirect_link' => home_url(),
            'redirect_log' => 1,
            'redirect_to' => 'link',
            'redirect_page' => '',
            'email_notify' => 0,
            'email_notify_address' => get_option( 'admin_email' ),
            'exclude_paths' => '/wp-content'
        );
        
        // If not already exist, adding values
        if ( ! get_option( 'i4t3_gnrl_options' ) ) {
            update_option( 'i4t3_gnrl_options' , $options );
        } else {
            // get old values if exists
            $old = get_option( 'i4t3_gnrl_options' );
            // loop through each new options
            // to check old value exist for each items.
            foreach ( $options as $key => $value ) {
                if ( array_key_exists( $key, $old ) ) {
                    // if old value exists, update that
                    $options[ $key ] = $old[ $key ];
                }
            }
            // update the old options
            update_option( 'i4t3_gnrl_options', $options );
        }
        // get plugin db version
        $db_version = get_option('i4t3_db_version');

        if ( ! $db_version || ( I4T3_DB_VERSION != $db_version ) ) {

            global $wpdb;
            
            $table = $wpdb->prefix . "404_to_301";

            $sql = "CREATE TABLE $table (
		id BIGINT NOT NULL AUTO_INCREMENT,
		date DATETIME NOT NULL,
		url VARCHAR(512) NOT NULL,
		ref VARCHAR(512) NOT NULL default '', 
		ip VARCHAR(40) NOT NULL default '',
		ua VARCHAR(512) NOT NULL default '',
                redirect VARCHAR(512) NULL default '',
		PRIMARY KEY  (id)
            );";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            // to be safe on db upgrades
            dbDelta($sql);
            // update db version
            update_option( 'i4t3_db_version', I4T3_DB_VERSION );
        }
    }
}
