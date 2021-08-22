<?php
/**
 * The current version DB class.
 *
 * This class has the latest database structure.
 * Create tables required for the plugin logs.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database
 * @subpackage Versions\Current
 */

namespace DuckDev\Redirect\Database;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Settings;
use DuckDev\Redirect\Utils\Base;

/**
 * Class Current.
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Database\Versions
 */
class Installer extends Base {

	/**
	 * Get current table names.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function tables() {
		return array(
			'logs'      => Database::instance()->get_table_name( '404_to_301_logs' ),
			'options'   => Database::instance()->get_table_name( '404_to_301_options' ),
			'redirects' => Database::instance()->get_table_name( '404_to_301_redirects' ),
		);
	}

	/**
	 * Get current table names.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function fields() {
		return array(
			'logs'      => array(
				'id'         => '%d',
				'url'        => '%s',
				'referrer'   => '%s',
				'ip'         => '%s',
				'agent'      => '%s',
				'method'     => '%s',
				'request'    => '%s',
				'created_at' => '%d',
			),
			'options'   => array(
				'id'              => '%d',
				'log_id'          => '%d',
				'redirect_id'     => '%d',
				'redirect_status' => '%s',
				'log_status'      => '%s',
				'email_status'    => '%s',
				'created_at'      => '%d',
				'updated_at'      => '%d',
				'updated_by'      => '%d',
			),
			'redirects' => array(
				'id'          => '%d',
				'source'      => '%s',
				'destination' => '%s',
				'code'        => '%d',
				'options'     => '%s',
				'status'      => '%s',
				'created_at'  => '%d',
				'updated_at'  => '%d',
				'created_by'  => '%d',
				'updated_by'  => '%d',
			),
		);
	}

	/**
	 * Created the latest tables required.
	 *
	 * This method will not check if the table creation
	 * was successful.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function install() {
		global $wpdb;

		// Charset.
		$charset = $this->get_charset();

		// All table schema.
		$queries = array(
			$this->logs_table_sql( $charset ),
			$this->redirects_table_sql( $charset ),
			// Options table should be created last to add foreign keys.
			$this->options_table_sql( $charset ),
		);

		// Create all tables.
		foreach ( $queries as $sql ) {
			// Remove unwanted chars.
			$sql = preg_replace( '/[ \t]{2,}/', '', $sql );
			// Run SQL query.
			$wpdb->query( $sql ); // phpcs:ignore
		}

		/**
		 * Action hook to trigger after creating tables.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_table_create' );

		return true;
	}

	/**
	 * Created the latest tables required.
	 *
	 * This method will not check if the table creation
	 * was successful.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function uninstall() {
		global $wpdb;

		foreach ( array_reverse( $this->tables() ) as $key => $name ) {
			// Drop table.
			$wpdb->query( "DROP TABLE IF EXISTS {$name}" ); // phpcs:ignore
		}

		// Delete options.
		delete_option( Settings::KEY );

		/**
		 * Action hook to trigger after removing tables and options.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_uninstall' );
	}

	/**
	 * Return any tables that are missing from the database.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array Array of missing table names.
	 */
	public function missing_tables() {
		global $wpdb;

		$missing = array();

		foreach ( $this->tables() as $key => $name ) {
			// Check if exists.
			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $name ) );
			// Add to missing list.
			if ( $wpdb->get_var( $query ) !== $name ) { // phpcs:ignore
				$missing[] = $name;
			}
		}

		return $missing;
	}

	/**
	 * SQL query to create logs table.
	 *
	 * @param string $charset Charset.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function logs_table_sql( $charset ) {
		$table = $this->tables()['logs'];

		return "CREATE TABLE IF NOT EXISTS `{$table}` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`url` MEDIUMTEXT NOT NULL,
			`referrer` VARCHAR(255) DEFAULT NULL,
			`ip` VARCHAR(45) DEFAULT NULL,
			`agent` VARCHAR(255) DEFAULT NULL,
			`method` VARCHAR(10) DEFAULT NULL,
			`request` MEDIUMTEXT,
			`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
	  ) $charset";
	}

	/**
	 * SQL query to create redirects table.
	 *
	 * @param string $charset Charset.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function redirects_table_sql( $charset ) {
		$table = $this->tables()['redirects'];

		return "CREATE TABLE IF NOT EXISTS `{$table}` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`source` MEDIUMTEXT NOT NULL,
			`destination` MEDIUMTEXT NOT NULL,
			`code` INT(11) unsigned DEFAULT '301',
			`options` MEDIUMTEXT,
			`status` ENUM('enabled', 'disabled', 'ignored') DEFAULT 'enabled',
			`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` TIMESTAMP DEFAULT NULL,
			`created_by` INT(11) unsigned DEFAULT NULL,
			`updated_by` INT(11) unsigned DEFAULT NULL,
			PRIMARY KEY (`id`)
	  ) $charset";
	}

	/**
	 * SQL query to create logs options table.
	 *
	 * This should be called only after creating other 2 tables.
	 * Otherwise, foreign key mapping will fail.
	 *
	 * @param string $charset Charset.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function options_table_sql( $charset ) {
		$table           = $this->tables()['options'];
		$logs_table      = $this->tables()['logs'];
		$redirects_table = $this->tables()['redirects'];

		return "CREATE TABLE IF NOT EXISTS `{$table}` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`log_id` INT(11) unsigned DEFAULT NULL,
			`redirect_id` INT(11) unsigned DEFAULT NULL,
			`redirect_status` ENUM('global', 'enabled', 'disabled') DEFAULT 'global',
			`log_status` ENUM('global', 'enabled', 'disabled') DEFAULT 'global',
			`email_status` ENUM('global', 'enabled', 'disabled') DEFAULT 'global',
			`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` TIMESTAMP DEFAULT NULL,
			`updated_by` INT(11) unsigned DEFAULT NULL,
			CONSTRAINT options_unique_ids UNIQUE (log_id, redirect_id),
			CONSTRAINT fk_options_log_id FOREIGN KEY (log_id) REFERENCES {$logs_table}(id) ON DELETE CASCADE,
			CONSTRAINT fk_options_redirect_id FOREIGN KEY (redirect_id) REFERENCES {$redirects_table}(id) ON DELETE CASCADE,
			PRIMARY KEY (`id`)
	  ) $charset";
	}

	/**
	 * Returns the current database charset.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return string Database charset
	 */
	public function get_charset() {
		global $wpdb;

		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			// Fix some common invalid charset values.
			$fixes = array( 'utf-8', 'utf' );

			$charset = $wpdb->charset;
			if ( in_array( strtolower( $charset ), $fixes, true ) ) {
				$charset = 'utf8';
			}

			$charset_collate = "DEFAULT CHARACTER SET $charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE=$wpdb->collate";
		}

		return $charset_collate;
	}
}
