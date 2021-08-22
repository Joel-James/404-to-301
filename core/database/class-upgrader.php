<?php
/**
 * The upgrade process class.
 *
 * This class handles the upgrade processes using background processing.
 *
 * @since      1.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Upgrade
 * @subpackage Upgrade
 */

namespace DuckDev\Redirect\Database;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Process;

/**
 * Class Upgrade.
 *
 * @since   1.0.0
 * @extends Process
 * @package DuckDev\Redirect\Database
 */
class Upgrader extends Process {

	/**
	 * Holds the name of the background process action.
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access protected
	 */
	protected $action = 'db_upgrade';

	/**
	 * Start the upgrader.
	 *
	 * This should be called once. Once started,
	 * upgrader will continue by it's own.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function start() {
		if ( ! $this->is_upgrading() && ! $this->is_running() ) {
			// Set the flag.
			dd4t3_cache()->set_transient( 'db_upgrading', true );

			$this->maybe_upgrade();
		}
	}

	/**
	 * Check if a process is preparing.
	 *
	 * Check whether the upgrade process is being prepared.
	 * This is to avoid infinite loop.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	public function is_upgrading() {
		return (bool) dd4t3_cache()->get_transient( 'db_upgrading' );
	}

	/**
	 * Get the list of completed upgrade processes.
	 *
	 * This will be available only during the upgrade.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return array
	 */
	public function get_completed() {
		// Get the existing list.
		$completed = get_site_transient( "{$this->identifier}_completed" );

		return empty( $completed ) ? array() : (array) $completed;
	}

	/**
	 * Check if an upgrade process is completed.
	 *
	 * @param string $name Name of upgrade.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	public function is_completed( $name ) {
		// Check if current item is in list.
		return in_array( $name, $this->get_completed(), true );
	}

	/**
	 * Run upgrade for a batch of data.
	 *
	 * This will process a single item from the batch.
	 *
	 * @param array  $item  Item data to process.
	 * @param string $group Name of the process.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	protected function task( $item, $group ) {
		$upgrader = $this->get_upgrader( $group );

		if ( $upgrader ) {
			$upgrader->upgrade_task( $item );
		}

		return false;
	}

	/**
	 * Complete the upgrade queue task.
	 *
	 * Please note this will be the completion of one upgrade
	 * process. Not all upgrades.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function complete() {
		// Mark as completed.
		parent::complete();

		// Continue if required.
		$this->maybe_upgrade();
	}

	/**
	 * Check if upgrade is required and then upgrade.
	 *
	 * Run pre-upgrade checks and if needed set the upgrade
	 * process queue and run it in background.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function maybe_upgrade() {
		// Get available upgrades.
		$upgrades = $this->get_upgrades();

		// Go through each item.
		foreach ( $upgrades as $upgrader ) {
			// Already completed.
			if ( $this->is_completed( $upgrader->get_id() ) ) {
				continue;
			}

			// No need to upgrade.
			if ( ! $upgrader->should_upgrade() ) {
				$this->mark_completed( $upgrader->get_id() );
			}

			// Upgrade now.
			$this->upgrade( $upgrader );
		}

		// Is finished?.
		if ( count( $this->get_completed() ) === count( $upgrades ) ) {
			$this->finish();
		}
	}

	/**
	 * Run a single upgrade process now.
	 *
	 * This will get the next batch of data from the upgrader
	 * and set it to queue and start a background process.
	 *
	 * @param Upgrades\Upgrade $upgrader Upgrader instance.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	private function upgrade( $upgrader ) {
		// Get upgrade data.
		$data = $upgrader->get_data();

		// Upgrade data is empty, that means it's completed.
		if ( empty( $data ) ) {
			$this->mark_completed( $upgrader->get_id() );

			return;
		}

		// Set data to queue.
		$this->set_queue( $data );
		// Save the queue.
		$this->save( $upgrader->get_id() );

		// Run now.
		$this->dispatch();
	}

	/**
	 * Get the available upgrade processes.
	 *
	 * All plugins should override this method and return the
	 * upgrade processes.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return Upgrades\Upgrade[] Array of processes.
	 */
	private function get_upgrades() {
		return array(
			'v4_settings' => Upgrades\V4_Settings::instance(),
			'v4_logs'     => Upgrades\V4_Logs::instance(),
		);
	}

	/**
	 * Get the available upgrade processes.
	 *
	 * All plugins should override this method and return the
	 * upgrade processes.
	 *
	 * @param string $name Name of upgrader.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return Upgrades\Upgrade|bool Upgrader or false.
	 */
	private function get_upgrader( $name ) {
		$upgrades = $this->get_upgrades();

		return isset( $upgrades[ $name ] ) ? $upgrades[ $name ] : false;
	}

	/**
	 * Finish all the upgrade processes.
	 *
	 * This is called when all the registered upgrade processes
	 * are processed. You can use the action hook to run post-upgrade
	 * actions in your plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	private function finish() {
		// Delete the transient.
		delete_site_transient( "{$this->identifier}_completed" );
		delete_site_transient( "{$this->identifier}_started" );

		/**
		 * Action hook to run after all processed completed.
		 *
		 * This will be executed once all the items in the upgrade
		 * queue is processed. Use this hook to mark the upgrade
		 * as completed in your plugin.
		 *
		 * @param string $action Current action name.
		 *
		 * @since 1.0.0
		 */
		do_action( 'dd4t3_db_upgrade_finished', $this->action );
	}

	/**
	 * Mark a process in current upgrade list as completed.
	 *
	 * Set a flag in cache so that we can skip it later.
	 *
	 * @param string $name Name of the upgrade.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	private function mark_completed( $name ) {
		$upgrader = $this->get_upgrader( $name );

		if ( $upgrader ) {
			$upgrader->upgrade_complete();
		}

		// Get the existing list.
		$completed = $this->get_completed();

		// Add current item.
		$completed[] = $name;

		// Update transient.
		set_site_transient( "{$this->identifier}_completed", $completed );
	}
}
