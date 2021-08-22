<?php
/**
 * Upgrader class for v4.0.0
 *
 * Upgrade old error logs to new table structure.
 * We have 3 new tables in v4.0.0. Upgrade the data
 * and delete the old table.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database
 * @subpackage Upgrades\V4
 */

namespace DuckDev\Redirect\Database\Upgrades;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\QueryBuilder\Query;

/**
 * Class V4.
 *
 * @since   4.0.0
 * @extends Upgrade
 * @package DuckDev\Redirect\Database\Upgrades
 */
class V4_Logs extends Upgrade {

	/**
	 * Holds a unique name for the process.
	 *
	 * @var string $id
	 *
	 * @since  4.0.0
	 * @access protected
	 */
	protected $id = 'v4_logs';

	/**
	 * Limit the no. of logs to process at a time.
	 *
	 * @var int $limit
	 *
	 * @since  4.0.0
	 * @access private
	 */
	private $limit = 100;

	/**
	 * Check if we can continue with upgrade.
	 *
	 * Continue only if old table exist.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function should_upgrade() {
		return $this->old_table_exist();
	}

	/**
	 * Run upgrade process complete actions.
	 *
	 * Delete the logs table when completed.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade_complete() {
		$this->do_query( "DROP TABLE IF EXISTS {$this->get_old_table_name()}" );
	}

	/**
	 * Upgrade old logs to new tables.
	 *
	 * In older versions, we had only one table to store
	 * all logs, options and redirects. Move those data
	 * to new separate tables.
	 * This can be a huge task depending on the no. of logs
	 * available on the site. So do it as a background process
	 * with batches.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array|false
	 */
	public function get_data() {
		$settings = $this->get_old_settings();

		if ( $settings ) {
			return array( $settings );
		}

		// Get first 500 log ids.
		$ids = $this->get_next_ids();

		return empty( $ids ) ? false : $ids;
	}

	/**
	 * Upgrade a log item to new structure.
	 *
	 * Move old to logs, options and redirects tables.
	 *
	 * @param int $id Log ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade_task( $id ) {
		error_log( $id );
		// Get old log.
		if ( ! empty( $id ) ) {
			$log = $this->get_old_log( $id );

			if ( ! empty( $log ) ) {
				// Get options.
				$options = $this->get_value( 'options', $log, array() );
				$options = empty( $options ) ? array() : maybe_unserialize( $options );

				// Create new log.
				$log_id = $this->create_log( $log );
				// Create redirect if required.
				$redirect_id = $this->create_redirect( $log, $options );
				// Options can be created only if log is created.
				if ( ! empty( $log_id ) ) {
					// Options should be created last.
					$this->create_options( $options, $log_id, $redirect_id );
				}

				// Now delete it.
				$this->delete_log( $id );
			}
		}
	}

	/**
	 * Create new log item from old data.
	 *
	 * Few field names has been changed and options
	 * are moved to separate table.
	 *
	 * @param array $log Log data.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool|int
	 */
	private function create_log( $log ) {
		global $wpdb;

		// Get the url.
		$url = $this->get_value( 'url', $log, '' );

		// URL is required.
		if ( ! empty( $url ) ) {
			// phpcs:ignore
			$insert = $wpdb->insert(
				$this->get_table_name( '404_to_301_logs' ),
				array(
					'url'        => esc_url_raw( $url ),
					'referrer'   => $this->get_value( 'ref', $log ),
					'ip'         => $this->get_value( 'ip', $log ),
					'agent'      => $this->get_value( 'ua', $log ),
					'method'     => 'GET',
					'created_at' => $this->get_value( 'date', $log, current_time( 'mysql' ) ),
				)
			);

			// Return ID if success or false.
			return empty( $insert ) ? false : $wpdb->insert_id;
		}

		return false;
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
	 * @return bool|int
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
				$this->get_table_name( '404_to_301_redirects' ),
				array(
					'source'      => $source,
					'destination' => $destination,
					'code'        => (int) $code,
					'status'      => 'enabled',
					'created_at'  => current_time( 'mysql' ),
				)
			);

			return empty( $insert ) ? false : $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Upgrade a log options to new table.
	 *
	 * Options can be created only when parent log
	 * item is created.
	 *
	 * @param array $options     Old options.
	 * @param int   $log_id      Login ID.
	 * @param int   $redirect_id Redirect id.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function create_options( array $options, $log_id, $redirect_id ) {
		global $wpdb;

		if ( empty( $log_id ) ) {
			return;
		}

		// Set options data.
		$data = array(
			'log_id'          => (int) $log_id,
			'redirect_status' => isset( $options['redirect'] ) ? $this->get_status( $options['redirect'] ) : 'global',
			'log_status'      => isset( $options['redirect'] ) ? $this->get_status( $options['log'] ) : 'global',
			'email_status'    => isset( $options['alert'] ) ? $this->get_status( $options['alert'] ) : 'global',
		);

		// If redirect id is found add that too.
		if ( ! empty( $redirect_id ) ) {
			$data['redirect_id'] = (int) $redirect_id;
		}

		// Now create options.
		$wpdb->insert( // phpcs:ignore
			$this->get_table_name( '404_to_301_options' ),
			$data
		);
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
	 * Get old log ids for creating batch.
	 *
	 * Get next 500 log ids to process. We are creating
	 * small batches of 500 logs to not break the db.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array Log Ids.
	 */
	private function get_old_settings() {
		return get_option( 'i4t3_gnrl_options' );
	}

	/**
	 * Get old log ids for creating batch.
	 *
	 * Get next 500 log ids to process. We are creating
	 * small batches of 500 logs to not break the db.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array Log Ids.
	 */
	private function get_next_ids() {
		return Query::init( __METHOD__ )
			->from( '404_to_301' )
			->select( 'id' ) // Select only id.
			->limit( 0, $this->limit )
			->column();
	}

	/**
	 * Delete the first 500 logs from old table.
	 *
	 * These logs are already processed. We need to
	 * clean them before processing next batch.
	 *
	 * @param int $id Log ID.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool
	 */
	private function delete_log( $id ) {
		try {
			return Query::init( __METHOD__ )
				->from( '404_to_301' )
				->where( 'id', $id )
				->delete();
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Get the table name of old logs table.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function get_old_table_name() {
		return $this->get_table_name( '404_to_301' );
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
	private function old_table_exist() {
		return true;
		global $wpdb;
		// Table name.
		$table = $this->get_old_table_name();

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );

		return $wpdb->get_var( $query ) === $table; // phpcs:ignore
	}

	/**
	 * Get the old log item from old table.
	 *
	 * @param int $id Log item id.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_old_log( $id ) {
		try {
			return Query::init( __METHOD__ . $id )
				->from( '404_to_301' )
				->where( 'id', $id )
				->one( ARRAY_A );
		} catch ( \Exception $e ) {
			return array();
		}
	}
}
