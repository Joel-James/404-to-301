<?php
/**
 * The error logs model class.
 *
 * This class handles the database queries for error logs.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    Model
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Database;

/**
 * Class Logs.
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Models
 * @extends Model
 */
class Logs extends Model {

	/**
	 * Get a log by ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int $log_id Log ID.
	 *
	 * @return object|false Log object if successful, false otherwise.
	 */
	public function get( $log_id ) {
		$logs = new Database\Queries\Log();

		// Return log.
		return $logs->get_item( $log_id );
	}

	/**
	 * Get a log by URL.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $url 404 url.
	 *
	 * @return object|false Log object if successful, false otherwise.
	 */
	public function get_by_url( $url ) {
		$logs = new Database\Queries\Log();

		return $logs->get_item_by( 'url', $url );
	}

	/**
	 * Get error logs.
	 *
	 * Return the log data from using the ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $args Filter items using fields.
	 *
	 * @return array
	 */
	public function get_logs( array $args = array() ) {
		// Parse args.
		$args = wp_parse_args(
			$args,
			array(
				'number' => 50,
			)
		);

		// Create a query object.
		$logs = new Database\Queries\Log();

		// Return logs.
		return $logs->query( $args );
	}

	/**
	 * Create a new error log.
	 *
	 * Make sure to validate all fields before adding it.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data Data.
	 *
	 * @return bool
	 */
	public function create( array $data ) {
		// Can not continue if url is empty.
		if ( empty( $data['url'] ) ) {
			return false;
		}

		// Create a query object.
		$logs = new Database\Queries\Log();

		// Create log.
		if ( $logs->add_item( $data ) ) {
			// Update the visits count if log already exists..
			if ( $this->get_by_url( $data['url'] ) ) {
				$this->mark_visit( $data['url'] );
			}

			/**
			 * Action hook fired after a new log is created.
			 *
			 * @since 4.0.0
			 *
			 * @param array $data Data used for log.
			 */
			do_action( 'dd4t3_model_after_log_create', $data );

			return true;
		}

		return false;
	}

	/**
	 * Update an existing log entry.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int   $log_id Log ID.
	 * @param array $data   Data.
	 *
	 * @return bool
	 */
	public function update( $log_id, array $data ) {
		// Can not continue if id is empty.
		if ( empty( $log_id ) ) {
			return false;
		}

		// Create a query object.
		$logs = new Database\Queries\Log();

		// Update log.
		if ( $logs->update_item( $log_id, $data ) ) {
			/**
			 * Action hook fired after a log is updated.
			 *
			 * @since 4.0.0
			 *
			 * @param int   $log_id Log ID.
			 * @param array $data   Data used for log.
			 */
			do_action( 'dd4t3_model_after_log_update', $log_id, $data );

			return true;
		}

		return false;
	}

	/**
	 * Update multiple log entries.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data  Data to update.
	 * @param array $where Where conditions.
	 *
	 * @return bool
	 */
	public function update_multiple( array $data, array $where ) {
		// Create a query object.
		$logs = new Database\Queries\Log();

		// Update log.
		if ( $logs->update_multiple( $data, $where ) ) {
			/**
			 * Action hook fired after a bulk log update.
			 *
			 * @since 4.0.0
			 *
			 * @param array $data  Data to update.
			 * @param array $where Where conditions.
			 */
			do_action( 'dd4t3_model_after_log_update_multiple', $data, $where );

			return true;
		}

		return false;
	}

	/**
	 * Delete a log entry.
	 *
	 * Deleting a log won't delete it's redirect.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int $log_id Log ID.
	 *
	 * @return bool
	 */
	public function delete( $log_id ) {
		// Can not continue if id is empty.
		if ( empty( $log_id ) ) {
			return false;
		}

		// Create a query object.
		$logs = new Database\Queries\Log();

		// Get log for action hook.
		$log = $this->get( $log_id );

		// Delete log.
		if ( $log && $logs->delete_item( $log_id ) ) {
			/**
			 * Action hook fired after a log is deleted.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $log_id Log ID.
			 * @param object $log    Log data.
			 */
			do_action( 'dd4t3_model_after_log_delete', $log_id, $log );

			return true;
		}

		return false;
	}

	/**
	 * Get the visits count for a 404 URL.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $url URL.
	 *
	 * @return bool
	 */
	public function get_visits( $url ) {
		// Can not continue if url is empty.
		if ( empty( $url ) ) {
			return false;
		}

		// Get log by URL.
		$log = $this->get_by_url( $url );

		// If 0 visits, no log found.
		return isset( $log->visits ) ? (int) $log->visits : 0;
	}

	/**
	 * Increment visits count for a log.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $url URL of the request.
	 *
	 * @return bool
	 */
	public function mark_visit( $url ) {
		// Can not continue if url is empty.
		if ( empty( $url ) ) {
			return false;
		}

		// Get current visits.
		$visits = $this->get_visits( $url );

		// Increment the visits count.
		if ( $this->update_multiple(
			array( 'visits' => $visits + 1 ),
			array( 'url' => $url )
		) ) {
			/**
			 * Action hook executed after incrementing a log's visits count.
			 *
			 * @since 4.0.0
			 *
			 * @param bool $success Is visits update success.
			 */
			do_action( 'dd4t3_model_after_mark_visit', $visits );

			return true;
		}

		return false;
	}
}
