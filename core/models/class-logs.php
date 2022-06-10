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
	 * Get a log by url.
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
		return $logs->add_item( $data );
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

		// Create log.
		return $logs->update_item( $log_id, $data );
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

		// Delete log.
		return $logs->delete_item( $log_id );
	}

	/**
	 * Update multiple logs using where conditions.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data  Data to update.
	 * @param array $where Where conditions.
	 *
	 * @return bool
	 */
	public function update_logs( array $data, array $where ) {
		// Create a query object.
		$logs = new Database\Queries\Log();

		return $logs->update_multiple( $data, $where );
	}
}
