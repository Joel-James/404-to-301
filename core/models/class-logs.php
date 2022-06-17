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
	 * Fields that can be updated.
	 *
	 * @since 4.0.0
	 * @var string[] $updatable
	 */
	protected $updatable = array(
		'meta',
		'visits',
		'log_status',
		'email_status',
		'redirect_status',
	);

	/**
	 * Initialize class and register hooks.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function __construct() {
		parent::__construct();

		// Handle redirect item changes.
		add_action( 'dd4t3_model_after_redirect_create', array( $this, 'on_redirect_create' ), 10, 2 );
		add_action( 'dd4t3_model_after_redirect_update', array( $this, 'on_redirect_update' ), 10, 2 );
		add_action( 'dd4t3_model_after_redirect_delete', array( $this, 'on_redirect_delete' ), 10, 2 );
	}

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
		$log = new Database\Queries\Log();

		// Return log.
		return $log->get_item( $log_id );
	}

	/**
	 * Get a log by URL.
	 *
	 * There can be multiple logs with same URL, but we will
	 * get only one here.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $url 404 url.
	 *
	 * @return object|false Log object if successful, false otherwise.
	 */
	public function get_by_url( $url ) {
		$log = new Database\Queries\Log();

		return $log->get_item_by( 'url', $url );
	}

	/**
	 * Get a log by redirect ID.
	 *
	 * There can be multiple logs with same redirect id, but we will
	 * get only one here.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int $redirect_id Redirect ID.
	 *
	 * @return object|false Log object if successful, false otherwise.
	 */
	public function get_by_redirect( $redirect_id ) {
		$log = new Database\Queries\Log();

		return $log->get_item_by( 'redirect_id', $redirect_id );
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
		$log = new Database\Queries\Log();

		// Return logs.
		return $log->query( $args );
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
		$log = new Database\Queries\Log();

		// Create log.
		if ( $log->add_item( $data ) ) {
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
		$log = new Database\Queries\Log();

		// Prepare data.
		$data = $this->prepare_fields( $data );

		// Update log.
		if ( ! empty( $data ) && $log->update_item( $log_id, $data ) ) {
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
		$log = new Database\Queries\Log();

		// Prepare data.
		$data = $this->prepare_fields( $data );

		// Update log.
		if ( $log->update_multiple( $data, $where ) ) {
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
		$log = new Database\Queries\Log();

		// Get log for action hook.
		$log_data = $this->get( $log_id );

		// Delete log.
		if ( $log_data && $log->delete_item( $log_id ) ) {
			/**
			 * Action hook fired after a log is deleted.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $log_id   Log ID.
			 * @param object $log_data Log data.
			 */
			do_action( 'dd4t3_model_after_log_delete', $log_id, $log_data );

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

	/**
	 * Link redirect id to log if required.
	 *
	 * When a new redirect is created check if the same source
	 * URL exist as URL in 404 error logs. If so, link the ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int   $redirect_id Redirect ID.
	 * @param array $data        Created log data.
	 *
	 * @return void
	 */
	public function on_redirect_create( $redirect_id, $data ) {
		// Link if redirect ID and URL found.
		if ( ! empty( $redirect_id ) && ! empty( $data['url'] ) ) {
			$this->link_redirect( $data['url'], $redirect_id );
		}
	}

	/**
	 * Link/unlink redirect id to log if required.
	 *
	 * When a new redirect is updated check if the same source
	 * URL exist as URL in 404 error logs. If so, link the ID.
	 * Also unlink old url if the redirect url is changed.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int   $redirect_id Redirect ID.
	 * @param array $data        Created log data.
	 *
	 * @return void
	 */
	public function on_redirect_update( $redirect_id, $data ) {
		// No need to continue if URL has not changed.
		if ( empty( $redirect_id ) || empty( $data['url'] ) ) {
			return;
		}

		// Get an old log for the redirect.
		$log = $this->get_by_redirect( $redirect_id );

		// Unlink from old logs.
		if ( isset( $log->id ) ) {
			$this->link_redirect( $log->url );
		}

		// Now link to matching logs.
		$this->link_redirect( $data['url'], $redirect_id );
	}

	/**
	 * Unlink redirect id to log if required.
	 *
	 * When a new redirect is deleted, unlink all logs from it.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int   $redirect_id Redirect ID.
	 * @param array $data        Created log data.
	 *
	 * @return void
	 */
	public function on_redirect_delete( $redirect_id, $data ) {
		// Unlink redirect if URL found.
		if ( isset( $data['url'] ) ) {
			$this->link_redirect( $data['url'] );
		}
	}

	/**
	 * Link or unlink a redirect ID to logs with same URL.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string   $url         URL.
	 * @param int|null $redirect_id Redirect ID (Unlink if null).
	 *
	 * @return void
	 */
	public function link_redirect( $url, $redirect_id = null ) {
		// Can not continue if url is empty.
		if ( empty( $url ) ) {
			return;
		}

		// Unlink from all logs.
		$this->update_multiple(
			array( 'redirect_id' => $redirect_id ),
			array( 'url' => $url )
		);
	}
}
