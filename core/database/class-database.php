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

use DuckDev\Redirect\Utils\Base;

/**
 * Class DB.
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Database
 */
class Database extends Base {

	/**
	 * Logs table object.
	 *
	 * @var Tables\Logs
	 * @access private
	 * @since  4.0.0
	 */
	private $logs;

	/**
	 * Options table object.
	 *
	 * @var Tables\Options
	 * @access private
	 * @since  4.0.0
	 */
	private $options;

	/**
	 * Redirects table object.
	 *
	 * @var Tables\Redirects
	 * @access private
	 * @since  4.0.0
	 */
	private $redirects;

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
		$this->logs      = new Tables\Logs();
		$this->options   = new Tables\Options();
		$this->redirects = new Tables\Redirects();
	}

	/**
	 * Initialize class and run install or upgrade.
	 *
	 * This class will always be initialized and the db install
	 * or upgrade will automatically happen.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return Tables\Logs
	 */
	public function logs() {
		return $this->logs;
	}

	/**
	 * Initialize class and run install or upgrade.
	 *
	 * This class will always be initialized and the db install
	 * or upgrade will automatically happen.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return Tables\Options
	 */
	public function options() {
		return $this->options;
	}

	/**
	 * Initialize class and run install or upgrade.
	 *
	 * This class will always be initialized and the db install
	 * or upgrade will automatically happen.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return Tables\Redirects
	 */
	public function redirects() {
		return $this->redirects;
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
		if ( ! $this->logs->exists() ) {
			$this->logs->install();
		}

		if ( ! $this->options->exists() ) {
			$this->options->install();
		}

		if ( ! $this->redirects->exists() ) {
			$this->redirects->install();
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
		if ( $this->logs->exists() ) {
			$this->logs->uninstall();
		}

		if ( $this->options->exists() ) {
			$this->options->uninstall();
		}

		if ( $this->redirects->exists() ) {
			$this->redirects->uninstall();
		}

		/**
		 * Action hook to run after db uninstall.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_db_after_uninstall' );
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
}
