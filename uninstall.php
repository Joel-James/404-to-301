<?php
/**
 * Fired during plugin uninstall.
 *
 * This file contains the cleanup after the plugin is uninstalled.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Uninstall
 */

// If this file is called directly, abort.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit();

// Delete settings.
delete_option( DuckDev\Redirect\Settings::KEY );
// DB versions.
delete_option( '404_to_301_logs_version' );
delete_option( '404_to_301_redirects_version' );

global $wpdb;

// Drop our custom table.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}404_to_301" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}404_to_301_options" );
