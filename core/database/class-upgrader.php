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

namespace RedirectPress\Database;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use RedirectPress\Permission;
use RedirectPress\Utils\Base;
use RedirectPress\Utils\Helpers;

/**
 * Class Upgrader.
 *
 * @since   4.0.0
 * @extends Base
 * @package RedirectPress\Database
 */
class Upgrader extends Base {

	/**
	 * Initialize the upgrader.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function init() {
		// Initialize upgrades.
		add_action( 'init', array( $this, 'logs_upgrade' ) );
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
		$version = redirectpress_settings()->get( 'plugin_version', 0 );

		// Only if the current version is higher than existing.
		if ( version_compare( REDIRECTPRESS_VERSION, $version, '>' ) ) {
			$settings = new Upgrades\Settings();
			$settings->upgrade( $version );

			// Update the plugin version.
			redirectpress_settings()->set( 'plugin_version', REDIRECTPRESS_VERSION );
		}
	}

	/**
	 * Init the logs upgrade class.
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
		// Always init.
		$logs = new Upgrades\Logs();

		if (
			is_admin() &&
			isset( $_GET['redirectpress_db_upgrade'], $_GET['redirectpress_nonce'] ) &&
			wp_verify_nonce( Helpers::input_get( 'redirectpress_nonce' ), 'redirectpress_db_upgrade' )
		) {
			// Perform logs upgrade.
			if ( Permission::has_access() ) {
				// Allowed actions.
				$actions = array( 'skip', 'upgrade_all', 'upgrade_redirects' );
				// Get the action.
				$action = Helpers::input_get( 'redirectpress_db_upgrade' );

				// Start upgrade.
				if ( in_array( $action, $actions, true ) ) {
					$logs->start( $action );
				}
			}
		}
	}
}
