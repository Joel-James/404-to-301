<?php
/**
 * The v4 logs upgrade process class.
 *
 * Upgrading old error logs to new tables using background processing.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Upgrade
 * @subpackage Upgrade
 */

namespace DuckDev\Redirect\Database\Upgrades;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Process;

/**
 * Class Upgrade.
 *
 * @since   4.0.0
 * @extends Process
 * @package DuckDev\Redirect\Database\Upgrades
 */
class Logs extends Process {

	/**
	 * Holds the name of the background process action.
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access protected
	 */
	protected $action = 'logs_upgrade';

	/**
	 * Start the upgrader.
	 *
	 * This should be called once. Once started,
	 * upgrader will continue by it's own.
	 *
	 * @param string $action Action name (skip, start).
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function start( $action ) {
		if ( ! $this->is_upgrading() && ! $this->is_running() && $this->table_exists() ) {
			// Set the flag.
			dd4t3_cache()->set_transient( 'logs_upgrading', true );

			// If skipped, remove old table.
			if ( 'skip' === $action ) {
				$this->drop_table();
			} else {
				// Cleanup normal logs.
				$this->clean_logs();

				// Upgrade customized logs.
				$this->upgrade_logs();
			}
		}
	}

	/**
	 * Check if a upgrade process is preparing.
	 *
	 * This is to avoid infinite loop.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	public function is_upgrading() {
		return (bool) dd4t3_cache()->get_transient( 'logs_upgrading' );
	}

	/**
	 * Run upgrade for a batch of data.
	 *
	 * This will process a single item from the batch.
	 * Move old to logs, options and redirects tables.
	 *
	 * @param int    $item  Log id.
	 * @param string $group Name of the process.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	protected function task( $item, $group ) {
		// Get old log.
		if ( ! empty( $item ) ) {
			$log = $this->get_old_log( $item );

			if ( ! empty( $log ) ) {
				// Get options.
				$options = $this->get_value( 'options', $log, array() );
				$options = empty( $options ) ? array() : maybe_unserialize( $options );

				// Create new log.
				$this->create_log( $log, $options );
				// Create redirect if required.
				$this->create_redirect( $log, $options );
			}
		}

		return false;
	}

	/**
	 * Complete the upgrade queue task.
	 *
	 * Drop the table and clear the upgrad flag.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function complete() {
		// Mark as completed.
		parent::complete();

		// Remove the table.
		$this->drop_table();

		// Delete the flag.
		dd4t3_cache()->delete_transient( 'logs_upgrading' );
	}

	/**
	 * Run a single upgrade process now.
	 *
	 * This will get the next batch of data from the upgrader
	 * and set it to queue and start a background process.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	private function upgrade_logs() {
		// Get upgrade data.
		$ids = $this->get_log_ids();

		// Upgrade data is empty, that means it's completed.
		if ( empty( $ids ) ) {
			$this->complete();

			return;
		}

		// Set data to queue.
		$this->set_queue( $ids );
		// Save the queue.
		$this->save( 'logs_upgrade' );

		// Run now.
		$this->dispatch();
	}

	/**
	 * Create new log item from old data.
	 *
	 * Few field names has been changed and new fields are added.
	 *
	 * @param array $log     Log data.
	 * @param array $options Old options.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function create_log( $log, array $options ) {
		global $wpdb;

		// Get the url.
		$url = $this->get_value( 'url', $log, '' );

		// URL is required.
		if ( ! empty( $url ) ) {
			// phpcs:ignore
			$wpdb->insert(
				$this->table_name( '404_to_301_logs' ),
				array(
					'url'             => esc_url_raw( $url ),
					'referrer'        => $this->get_value( 'ref', $log ),
					'ip'              => $this->get_value( 'ip', $log ),
					'agent'           => $this->get_value( 'ua', $log ),
					'request_method'  => 'GET',
					'redirect_status' => $this->get_status( $this->get_value( 'redirect', $options, 2 ) ),
					'log_status'      => $this->get_status( $this->get_value( 'log', $options, 2 ) ),
					'email_status'    => $this->get_status( $this->get_value( 'alert', $options, 2 ) ),
					'created_at'      => $this->get_value( 'date', $log, current_time( 'mysql' ) ),
					'updated_at'      => current_time( 'mysql' ),
					'updated_by'      => function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0,
				)
			);
		}
	}

	/**
	 * Create new custom redirect from old data.
	 *
	 * If a custom redirect link is found, create an item
	 * for our redirects table.
	 *
	 * @param array $log     Log item.
	 * @param array $options Old options.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function create_redirect( $log, array $options ) {
		global $wpdb;

		// Redirect from.
		$source = $this->get_value( 'url', $log );
		// Redirect to.
		$destination = $this->get_value( 'redirect', $log );

		// Only if source and destination can be set.
		if ( ! empty( $source ) && ! empty( $destination ) ) {
			if ( empty( $options['type'] ) ) {
				$code = dd4t3_settings()->get( 'type', 'redirect', 301 );
			} else {
				$code = $options['type'];
			}

			// Insert new redirect.
			$insert = $wpdb->insert( // phpcs:ignore
				$this->table_name( '404_to_301_redirects' ),
				array(
					'source'      => $source,
					'destination' => $destination,
					'code'        => (int) $code,
					'status'      => 'enabled',
					'type'        => 'url',
					'created_at'  => current_time( 'mysql' ), // Not correct.
				)
			);
		}
	}

	/**
	 * Helper method to get a new status value from old one.
	 *
	 * @param mixed $value Value to check.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function get_status( $value ) {
		$value = (int) $value;
		switch ( $value ) {
			case 0:
				return 'disabled';
			case 1:
				return 'enabled';
			default:
				return 'global';
		}
	}

	/**
	 * Get an item from data array.
	 *
	 * If not found, return default value.
	 *
	 * @param string $key     Item key.
	 * @param array  $data    Data to check.
	 * @param mixed  $default Default value.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return mixed
	 */
	protected function get_value( $key, $data, $default = '' ) {
		return isset( $data[ $key ] ) ? $data[ $key ] : $default;
	}

	/**
	 * Get old log ids for creating batch.
	 *
	 * Get all log ids because only customized logs are available
	 * in logs table.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array|bool Log Ids.
	 */
	private function get_log_ids() {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		// phpcs:ignore
		$ids = $wpdb->get_col( "SELECT id FROM $table" );

		return empty( $ids ) ? false : $ids;
	}

	/**
	 * Get the old log item from old table.
	 *
	 * @param int $id Log item id.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array|bool
	 */
	private function get_old_log( $id ) {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		// phpcs:ignore
		$log = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

		return empty( $log ) ? false : $log;
	}

	/**
	 * Clean old logs table to remove all logs except customized ones.
	 *
	 * We need to upgrade only the logs with custom options set.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	private function clean_logs() {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		// phpcs:ignore
		$wpdb->query( "DELETE FROM $table WHERE options IS NULL AND redirect = ''" );
	}

	/**
	 * Remove the old logs table after upgrading all logs.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	private function drop_table() {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		// phpcs:ignore
		$wpdb->query( "DROP TABLE IF EXISTS $table" );
	}

	/**
	 * Check if old logs table exist.
	 *
	 * We need to run upgrade only if the table exist.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool
	 */
	public function table_exists() {
		static $exists = null;

		if ( null === $exists ) {
			global $wpdb;
			// Table name.
			$table = $this->table_name( '404_to_301' );

			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );

			$exists = $wpdb->get_var( $query ) === $table; // phpcs:ignore
		}

		return $exists;
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * @param string $table Table key.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function table_name( $table ) {
		global $wpdb;

		return $wpdb->prefix . $table;
	}
}
