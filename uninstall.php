<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * Fired during plugin uninstall.
 *
 * Remove all our settings, and custom database tables of user removes
 * our plugin.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage Uninstaller
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */

// Plugin option names.
$options = array(
	'i4t3_gnrl_options',
	'i4t3_activated_time',
	'i4t3_db_version',
	'i4t3_version_no',
);

// Delete all options.
foreach ( $options as $option ) {
	delete_option( $option );
}

global $wpdb;

// drop our custom table
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "404_to_301" );

/******* The end. Thanks for using 404 to 301 plugin ********/
