<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 * We will register our default settings here if not exists already.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage Activator
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */
class JJ4T3_Activator_Deactivator_Uninstaller {

	/**
	 * Function to run during activation
	 *
	 * We register default options to the WordPress if not exists already.
	 * We will keep the old values if already exist.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public static function activate() {
		// Default settings for our plugin.
		$options = array(
			'redirect_type'        => '301',
			'redirect_link'        => home_url(),
			'redirect_log'         => 1,
			'redirect_to'          => 'link',
			'redirect_page'        => '',
			'email_notify'         => 0,
			'disable_guessing'     => 0,
			'email_notify_address' => get_option( 'admin_email' ),
			'exclude_paths'        => '/wp-content',
		);

		// Get existing options if exists.
		$existing = get_option( 'i4t3_gnrl_options' );
		// Check if valid dcl settings exist.
		if ( $existing && is_array( $existing ) ) {
			foreach ( $options as $key => $value ) {
				if ( array_key_exists( $key, $existing ) ) {
					$options[ $key ] = $existing[ $key ];
				}
			}
		}

		// Update/create our settings.
		// We are using older prefix for our option names.
		update_option( 'i4t3_gnrl_options', $options );

		// Set review notice time for 1 week.
		add_option( 'i4t3_review_notice', time() + 604800 );

		// Manage error log table.
		self::log_table();
	}

	/**
	 * Create or update error logs table in database.
	 *
	 * Define our custom table schema and create the table if not exists.
	 * If already exists and changes found, update the table.
	 * dbDelta() will properly take care of these tasks safely.
	 *
	 * @global object $wpdb WordPress database helper object.
	 * @uses   dbDelta() For safe db upgrades.
	 *
	 * @return void
	 */
	private static function log_table() {

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
	}
}
