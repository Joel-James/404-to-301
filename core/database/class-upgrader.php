<?php
/**
 * The upgrade process class.
 *
 * This class will handle settings and logs upgrades.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Database
 * @subpackage Upgrader
 */

namespace DuckDev\Redirect\Database;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Permission;
use DuckDev\Redirect\Utils\Base;

/**
 * Class Upgrader.
 *
 * @since   1.0.0
 * @extends Base
 * @package DuckDev\Redirect\Database
 */
class Upgrader extends Base {

	/**
	 * Logs upgrader class.
	 *
	 * @since  4.0.0
	 * @access private
	 * @var Upgrades\Logs
	 */
	private $logs;

	/**
	 * Initialize the upgrader.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function init() {
		// This is a batch process, so it should always be initiated.
		$this->logs = new Upgrades\Logs();

		// Everything should be after admin init.
		add_action( 'admin_init', array( $this, 'logs_upgrade' ) );
		add_action( 'admin_init', array( $this, 'settings_upgrade' ) );
	}

	/**
	 * Start the settings upgrade.
	 *
	 * Different version upgrades should be handled inside settings
	 * upgrader class.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function settings_upgrade() {
		// Get current plugin version.
		$version = dd4t3_settings()->get( 'plugin_version', 0 );

		// Only if the current version is higher than existing.
		if ( version_compare( DD4T3_VERSION, $version, '>' ) ) {
			$settings = new Upgrades\Settings();
			$settings->upgrade( $version );

			// Update the plugin version.
			dd4t3_settings()->set( 'plugin_version', DD4T3_VERSION );
		}
	}

	/**
	 * Start the logs upgrade.
	 *
	 * Post v4 error logs should be upgraded only if user manually
	 * confirm. If skipped, delete logs and table.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function logs_upgrade() {
		if (
			isset( $_GET['dd4t3_db_upgrade'], $_GET['dd4t3_nonce'] ) &&
			wp_verify_nonce( $_GET['dd4t3_nonce'], 'dd4t3_db_upgrade' ) // phpcs:ignore
		) {
			// Perform logs upgrade.
			if ( Permission::has_access() ) {
				// Get the action.
				$action = 'upgrade' === $_GET['dd4t3_db_upgrade'] ? 'upgrade' : 'skip';
				// Start upgrade.
				$this->logs()->start( $action );
			}
		}
	}

	/**
	 * Get the logs upgrader instance.
	 *
	 * Useful to check the upgrade progress.
	 *
	 * @since 4.0.0
	 *
	 * @return Upgrades\Logs
	 */
	public function logs() {
		return $this->logs;
	}
}
