<?php
/**
 * The main database class.
 *
 * This class will handle all database maintenance actions.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    Core
 * @subpackage Database
 */

namespace DuckDev\Redirect\Database;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Base;

/**
 * Class Database.
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Database
 */
class Database extends Base {

	/**
	 * Custom database tables.
	 *
	 * @since  4.0.0
	 * @var    array
	 * @access private
	 */
	private $tables;

	/**
	 * Initialize class and run install or upgrade.
	 *
	 * This class will always be initialized and the db install
	 * or upgrade will automatically happen.
	 *
	 * @since  4.0.0
	 * @access public
	 */
	protected function init() {
		$this->tables = array(
			'logs'      => new Tables\Logs(),
			'redirects' => new Tables\Redirects(),
		);

		// Upgrader.
		if ( is_admin() ) {
			Upgrader::instance();
		}
	}

	/**
	 * Get the error logs database table class.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return Tables\Logs
	 */
	public function logs() {
		return $this->tables['logs'];
	}

	/**
	 * Get the redirects' database table class.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return Tables\Redirects
	 */
	public function redirects() {
		return $this->tables['redirects'];
	}

	/**
	 * Get all table class objects.
	 *
	 * This is a getter method to access tables property.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function tables() {
		return $this->tables;
	}

	/**
	 * Install database tables and data.
	 *
	 * Use this to manually check if all database tables
	 * exists and if not, install.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function install() {
		foreach ( $this->tables as $table ) {
			if ( ! $table->exists() ) {
				$table->install();
			}
		}

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
	 * Use this to manually check if all database tables
	 * needs upgrade and then upgrade.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade() {
		foreach ( $this->tables as $table ) {
			$table->maybe_upgrade();
		}

		/**
		 * Action hook to run after db upgrade.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_upgrade' );
	}

	/**
	 * Remove all database tables from db.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function uninstall() {
		foreach ( $this->tables as $table ) {
			if ( $table->exists() ) {
				$table->uninstall();
			}
		}

		/**
		 * Action hook to run after db uninstall.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_uninstall' );
	}
}
