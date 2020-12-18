<?php
/**
 * Fired during plugin uninstall.
 *
 * This file contains the cleanup after the plugin is uninstalled.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage Uninstall
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */

// If uninstall not called from WordPress exit.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit();

// Plugin option names.
$options = array(
	'i4t3_gnrl_options',
	'i4t3_activated_time',
	'i4t3_db_version',
	'i4t3_version_no',
	'i4t3_review_notice',
);

// Loop through each options.
foreach ( $options as $option ) {
	delete_option( $option );
}

global $wpdb;

// Drop our custom table.
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "404_to_301" );
