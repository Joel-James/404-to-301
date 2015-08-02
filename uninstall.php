<?php

/**
 * Fired only when the 404 to 301 is uninstalled.
 *
 * Removes everything that 404 to 301 added to your db.
 *
 *
 * @link		http://iscode.co/product/404-to-301/
 * @since		2.0.0
 * @author		Joel James
 * @package		I4T3
 */

// If uninstall not called from WordPress, then exit. That's it!

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options
if( get_option( 'i4t3_gnrl_options' ) ) {
	delete_option( 'i4t3_gnrl_options' );
}

// Drop tables
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "404_to_301" );

/******* The end. Thanks for using 404 to 301 plugin ********/
