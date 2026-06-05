<?php
/**
 * Plugin uninstall handler.
 *
 * Runs when the user clicks "Delete" on the plugin in
 * `wp-admin/plugins.php`. Removes every database trace the plugin
 * leaves behind:
 *  - the v4 settings option,
 *  - the v4 custom tables (logs + redirects),
 *  - the legacy v3 `404_to_301` table and its options (in case the
 *    user uninstalls before migrating),
 *  - dismissed-notice user meta.
 *
 * Intentionally side-effect-free aside from those `DELETE`s and
 * `DROP TABLE`s — the file stays readable so audits can confirm
 * "deleting the plugin actually deletes the plugin's data".
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

// Exit unless the file was loaded by WordPress's uninstall handler.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// v4 settings option.
delete_option( '404_to_301_settings' );

// BerlinDB schema-version markers.
delete_option( 'wpdb_404_to_301_logs_version' );
delete_option( 'wpdb_404_to_301_redirects_version' );

// Legacy v3 options.
delete_option( 'i4t3_gnrl_options' );
delete_option( 'i4t3_activated_time' );
delete_option( 'i4t3_db_version' );
delete_option( 'i4t3_version_no' );
delete_option( 'i4t3_review_notice' );

global $wpdb;

// v4 tables.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}404_to_301_logs" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}404_to_301_redirects" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

// Legacy v3 table (kept around in case the user uninstalls before migrating).
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}404_to_301" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

// Per-user dismissed-notice flags.
delete_metadata( 'user', 0, 'i4t3_review_notice_dismissed', '', true );
delete_metadata( 'user', 0, '404_to_301_review_dismissed', '', true );
delete_metadata( 'user', 0, '404_to_301_migration_dismissed', '', true );
