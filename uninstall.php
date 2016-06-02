<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die('Damn it.! Dude you are looking for what?');
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage Uninstaller
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */

// Deletes plugin options
$options = array(
    'i4t3_gnrl_options',
    'i4t3_db_version',
    'i4t3_version_no',
    'i4t3_agreement'
);
foreach ( $options as $option ) {
    if ( get_option( $option ) ) {
        delete_option( $option );
    }
}

global $wpdb;

// drop our custom table
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "404_to_301" );

/******* The end. Thanks for using 404 to 301 plugin ********/
