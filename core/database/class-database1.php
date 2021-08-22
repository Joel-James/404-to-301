<?php
/**
 * The main database class.
 *
 * This class will handle all database maintenance actions.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Database
 */

namespace DuckDev\Redirect\Database;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\QueryBuilder\Query;
use DuckDev\Redirect\Utils\Base;

/**
 * Class DB.
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Database
 */
class Database1 extends Base {

	/**
	 * Initialize class and run install or upgrade.
	 *
	 * This class will always be initialized and the db install
	 * or upgrade will automatically happen.
	 *
	 * @since  4.0.0
	 * @access public
	 */
	public function init() {
		// Post install/upgrade actions.
		//add_action( 'dd4t3_db_after_install', array( $this, 'after_install' ) );
		//add_action( 'dd4t3_db_upgrade_finished', array( $this, 'after_upgrade' ) );

		//if ( $this->needs_install() ) {
		//	// Install db.
		//	$this->install();
		//}

		// Upgrade actions.
		//add_action( 'admin_init', array( $this, 'upgrade' ) );

		$logs      = new Tables\Logs();
		$options   = new Tables\Options();
		$redirects = new Tables\Redirects();
		if ( ! $logs->exists() ) {
			$logs->install();
		}
		if ( ! $redirects->exists() ) {
			$redirects->install();
		}
		if ( ! $options->exists() ) {
			$options->install();
		}
	}

	/**
	 * Does the database need install?.
	 *
	 * If database version is not set or empty, we need
	 * to install the tables.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function needs_install() {
		return false;
		// Previous db version.
		$version = dd4t3_settings()->get( 'db_version', 'misc' );

		// If version is not set, install required.
		$required = empty( $version );

		/**
		 * Filter to modify db install check logic.
		 *
		 * @param bool $required Upgrade required?.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_db_needs_install', $required );
	}

	/**
	 * Check if a database upgrade is required.
	 *
	 * Database upgrade is required when existing db
	 * version is lower than new db version.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function needs_upgrade() {
		$required = false;

		// If install is already required, no upgrade.
		if ( ! $this->needs_install() ) {
			// Previous db version.
			$version = dd4t3_settings()->get( 'db_version', 'misc', 0 );
			// If previous version is lower.
			$required = version_compare( $version, DD4T3_DB_VERSION, '<' );
		}

		/**
		 * Filter to modify db upgrade check logic.
		 *
		 * @param bool $required Upgrade required?.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_db_needs_upgrade', $required );
	}

	/**
	 * Install database tables and data.
	 *
	 * Call this after checking DB::needs_install() to avoid
	 * unnecessary issues with data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function install() {
		// Set the flag.
		dd4t3_cache()->set_transient( 'db_installing', true );

		// Perform install actions.
		$actions = new Installer();
		$actions->install();

		/**
		 * Action hook to run after db install.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_install' );
	}

	/**
	 * Upgrade database tables and data.
	 *
	 * Call this after checking DB::needs_upgrade() to avoid
	 * unnecessary issues with data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade() {
		if ( isset( $_GET['upgrade_db'] ) ) {
			// Initialize upgrader.
			Upgrader::get()->start();
		}
	}

	/**
	 * Remove tables and settings from database.
	 *
	 * This will remove all database tables and plugin
	 * settings from the db. To perform a reset, call this
	 * and call install again.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function uninstall() {
		// Set the flag.
		dd4t3_cache()->set_transient( 'db_uninstalling', true );

		$installer = new Installer();
		$installer->uninstall();

		// Remove the flag.
		dd4t3_cache()->delete_transient( 'db_uninstalling' );

		/**
		 * Action hook to run after db uninstall.
		 *
		 * @param Installer $installer Current version db class instance.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_uninstall', $installer );
	}

	/**
	 * Remove tables and settings from database.
	 *
	 * This will remove all database tables and plugin
	 * settings from the db. To perform a reset, call this
	 * and call install again.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function deactivate() {
		/**
		 * Action hook to run after db uninstall.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_deactivate' );
	}

	/**
	 * Post db install actions.
	 *
	 * Reset the flag about install in progress.
	 * Set the latest db version.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function after_install() {
		// Remove the flag.
		dd4t3_cache()->delete_transient( 'db_installing' );

		// Set db version.
		dd4t3_settings()->update( 'db_version', DD4T3_DB_VERSION, 'misc' );
	}

	/**
	 * Upgrade database tables and data.
	 *
	 * Call this after checking DB::needs_upgrade() to avoid
	 * unnecessary issues with data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function after_upgrade() {
		// Remove the flag.
		dd4t3_cache()->delete_transient( 'db_upgrading' );

		// Set db version.
		dd4t3_settings()->update( 'db_version', DD4T3_DB_VERSION, 'misc' );
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * @param string $table  Table key.
	 * @param bool   $prefix Should prefix?.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_table_name( $table, $prefix = true ) {
		if ( $prefix ) {
			global $wpdb;

			$table = $wpdb->prefix . $table;
		}

		return $table;
	}
}
