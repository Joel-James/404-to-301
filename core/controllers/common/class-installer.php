<?php

namespace DuckDev404\Core\Controllers\Common;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Fired during plugin activation and deactivation.
 *
 * This class defines all code necessary for installation.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 **/
class Installer {

	/**
	 * Function that runs during plugin activation.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public static function activate() {
		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'This plugin requires a minimum PHP Version of 5.6', '404-to-301' ) );
		}

		// Create tables.
		self::create_tables();
	}

	/**
	 * Create custom tables for 404 to 301.
	 *
	 * Use dbDelta function to upgrade or create tables
	 * safely without breaking anything.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private static function create_tables() {
		// Create custom tables required for this plugin.
		// Get db version number.
		$db = get_option( 'i4t3_db_version' );

		// If table is upto date, do nothing.
		if ( defined( JJ4T3_DB_VERSION ) && $db === JJ4T3_DB_VERSION ) {
			return;
		}

		global $wpdb;
		// Out custom table name.
		$table = $wpdb->prefix . "404_to_301";

		// Define the table schema query.
		$query = "CREATE TABLE $table (
            id BIGINT NOT NULL AUTO_INCREMENT,
            date DATETIME NOT NULL,
            url VARCHAR(512) NOT NULL,
            ref VARCHAR(512) NOT NULL default '',
            ip VARCHAR(40) NOT NULL default '',
            ua VARCHAR(512) NOT NULL default '',
            redirect VARCHAR(512) NULL default '',
			options LONGTEXT,
			status BIGINT NOT NULL default 1,
            PRIMARY KEY  (id)
        );";

		// Handle DB upgrades in proper WordPress way.
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Update or create table in database.
		dbDelta( $query );

		// Update the db version number.
		update_option( 'i4t3_db_version', JJ4T3_DB_VERSION );
	}
}
