<?php

namespace DuckDev\WP404\Controllers\Common;

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

		// Set flags.
		self::set_flags();
	}

	/**
	 * Safely handle the upgrades from old versions when required.
	 *
	 * Check version numbers and upgrade to new strucure.
	 *
	 * @since 4.0
	 */
	public static function upgrade() {
		// Upgrading from v3+.
		if ( get_option( 'i4t3_version_no', false ) ) {
			self::upgrade_4_0();
		}

		// Set flags.
		self::set_flags();
	}

	/**
	 * Fired during plugin uninstall.
	 *
	 * We need to clear database by deleting all custom options
	 * and our custom logs table.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function uninstall() {
		// Plugin option names.
		$options = array(
			'404_to_301_settings',
			'404_to_301_version',
			'404_to_301_review_notice',
		);

		// Loop through each options.
		foreach ( $options as $option ) {
			delete_option( $option );
		}

		global $wpdb;

		/**
		 * Drop our custom table.
		 *
		 * @noinspection SqlNoDataSourceInspection
		 */
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "404_to_301" );
	}

	/**
	 * Set the version numbers and other flags for the plugin.
	 *
	 * If plugin is updated, update the version flag. Set the
	 * review notice flag.
	 *
	 * @since 4.0
	 */
	private static function set_flags() {
		// Set review notice time for 1 week.
		add_option( '404_to_301_review_notice', time() + 604800 );

		// Get plugin version number.
		$version = get_option( '404_to_301_version' );

		// Update the plugin version number.
		if ( defined( DD404_VERSION ) && $version !== DD404_VERSION ) {
			update_option( '404_to_301_version', DD404_VERSION );
		}
	}

	/**
	 * Create custom tables for 404 to 301.
	 *
	 * Use dbDelta function to upgrade or create tables safely
	 * without breaking anything.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private static function create_tables() {
		// Get plugin version number.
		$version = get_option( '404_to_301_version' );

		// If table is upto date, do nothing.
		if ( defined( DD404_VERSION ) && $version === DD404_VERSION ) {
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
	}

	/**
	 * Upgrade plugin data to new 4.0 version.
	 *
	 * Settings name has been changed in version 4.
	 * So update those values as well as the user meta
	 * values for review notices.
	 *
	 * @since 4.0
	 */
	private static function upgrade_4_0() {
		// Get old settings.
		$settings = get_option( 'i4t3_gnrl_options' );

		// Change the settings name.
		if ( $settings ) {
			// Change name.
			if ( update_option( '404_to_301_settings', $settings ) ) {
				// Delete old settings.
				delete_option( '404_to_301_settings' );
				delete_option( 'i4t3_version_no' );
			}
		}
	}
}
