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

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Logs.
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Models
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
		'hits',
		'log_status',
		'redirect_id',
		'email_status',
		'redirect_status',
	);

	/**
	 * Query class for the model.
	 *
	 * @since 4.0.0
	 * @var string $query
	 */
	protected $query = '\\DuckDev\\FourNotFour\\Database\Queries\Log';

	/**
	 * Flag to check if hooks should be skipped.
	 *
	 * @var bool $skip_hooks
	 */
	private $skip_hooks = false;

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
		add_action( '404_to_301_model_after_redirect_create', array( $this, 'on_redirect_create' ), 10, 2 );
		add_action( '404_to_301_model_after_redirect_update', array( $this, 'on_redirect_update' ), 10, 2 );
		add_action( '404_to_301_model_after_redirect_delete', array( $this, 'on_redirect_delete' ), 10, 2 );

		// Handle log updates.
		add_action( 'model_after_log_create', array( $this, 'on_log_create' ), 10, 3 );
		add_action( 'model_after_log_update', array( $this, 'on_log_update' ), 10, 3 );
		add_filter( '404_to_301_model_log_create_data', array( $this, 'filter_log_data' ) );
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
		return $this->query()->get_item( $log_id );
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
		return $this->query()->get_item_by( 'url', $url );
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
		return $this->query()->get_item_by( 'redirect_id', $redirect_id );
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

		// Return logs.
		return $this->query()->query( $args );
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

		/**
		 * Filter to modify final data before log creation.
		 *
		 * @since 4.0.0
		 *
		 * @param array $data Data for log creation.
		 */
		$data = apply_filters( '404_to_301_model_log_create_data', $data );

		// Create new log.
		$log_id = $this->query()->add_item( $data );

		if ( ! empty( $log_id ) ) {
			// Get the created object.
			$log = $this->get( $log_id );

			/**
			 * Action hook fired after a new log is created.
			 *
			 * @since 4.0.0
			 *
			 * @param int   $log_id Log ID.
			 * @param array $log    New log.
			 * @param array $data   Data used for creation.
			 */
			do_action( '404_to_301_model_after_log_create', $log_id, $log, $data );

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

		/**
		 * Filter to modify final data before log update.
		 *
		 * @since 4.0.0
		 *
		 * @param array $data Data for log update.
		 */
		$data = apply_filters( '404_to_301_model_log_update_data', $data );

		// Prepare data.
		$data = $this->prepare_fields( $data );

		// Update log.
		if ( ! empty( $data ) && $this->query()->update_item( $log_id, $data ) ) {
			// Get the updated object.
			$log = $this->get( $log_id );

			/**
			 * Action hook fired after a log is updated.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $log_id Log ID.
			 * @param object $log    Updated log.
			 * @param array  $data   Data used for update.
			 */
			do_action( '404_to_301_model_after_log_update', $log_id, $log, $data );

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
		// Prepare data.
		$data = $this->prepare_fields( $data );

		// Update log.
		if ( $this->query()->update_multiple( $data, $where ) ) {
			/**
			 * Action hook fired after a bulk log update.
			 *
			 * @since 4.0.0
			 *
			 * @param array $data  Data to update.
			 * @param array $where Where conditions.
			 */
			do_action( '404_to_301_model_after_log_update_multiple', $data, $where );

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

		// Get log for action hook.
		$log_data = $this->get( $log_id );

		// Delete log.
		if ( $log_data && $this->query()->delete_item( $log_id ) ) {
			/**
			 * Action hook fired after a log is deleted.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $log_id   Log ID.
			 * @param object $log_data Log data.
			 */
			do_action( '404_to_301_model_after_log_delete', $log_id, $log_data );

			return true;
		}

		return false;
	}

	/**
	 * Get the hit count for a 404 URL.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $url URL.
	 *
	 * @return bool
	 */
	public function get_hits( $url ) {
		// Can not continue if url is empty.
		if ( empty( $url ) ) {
			return false;
		}

		// Get log by URL.
		$log = $this->get_by_url( $url );

		// If 0 hits, no log found.
		return isset( $log->hits ) ? (int) $log->hits : 0;
	}

	/**
	 * Increment hits count for a log.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $url URL of the request.
	 *
	 * @return bool
	 */
	public function mark_hit( $url ) {
		// Can not continue if url is empty.
		if ( empty( $url ) ) {
			return false;
		}

		// Get current hits.
		$hits = (int) $this->get_hits( $url );

		// Increment the hits count.
		if ( $this->update_multiple(
			array( 'hits' => $hits + 1 ),
			array( 'url' => $url )
		) ) {
			/**
			 * Action hook executed after incrementing a log's hits count.
			 *
			 * @since 4.0.0
			 *
			 * @param bool $success Is hits update success.
			 */
			do_action( '404_to_301_model_after_mark_hit', $hits );

			return true;
		}

		return false;
	}

	/**
	 * Link redirect id to log if required.
	 *
	 * When a new redirect is created check if the same source
	 * URL exist as URL in 404 error logs. If so, link the ID.
	 * NOTE: We are not using singe query to make all updates because
	 * for linking redirect_id, we need a separate query. This is because
	 * we can update multiple logs at a time.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int    $redirect_id Redirect ID.
	 * @param object $item        Created redirect item.
	 *
	 * @return void
	 */
	public function on_redirect_create( $redirect_id, $item ) {
		// Link if redirect ID and URL found.
		if ( ! empty( $redirect_id ) && isset( $item->source ) ) {
			$this->sync_redirect(
				$item->source,
				array(
					'redirect_id'     => $redirect_id,
					'redirect_status' => $item->status,
				)
			);
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
	 * @param int    $redirect_id Redirect ID.
	 * @param object $item        Updated redirect item.
	 *
	 * @return void
	 */
	public function on_redirect_update( $redirect_id, $item ) {
		// No need to continue if URL has not changed.
		if ( $this->skip_hooks || empty( $redirect_id ) || ! isset( $item->source ) ) {
			return;
		}

		// Get an old log for the redirect.
		$log = $this->get_by_redirect( $redirect_id );

		// Unlink from old logs if URL changed.
		if ( isset( $log->id ) && $log->url !== $item->source ) {
			// Unlink logs.
			$this->sync_redirect( $log->url, array( 'redirect_id' => null ) );
		}

		// Now link to matching logs.
		$this->sync_redirect(
			$item->source,
			array(
				'redirect_id'     => $redirect_id,
				'redirect_status' => $item->status,
			)
		);
	}

	/**
	 * Unlink redirect id to log if required.
	 *
	 * When a new redirect is deleted, unlink all logs from it.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int    $redirect_id Redirect ID.
	 * @param object $item        Deleted redirect item.
	 *
	 * @return void
	 */
	public function on_redirect_delete( $redirect_id, $item ) {
		// Unlink redirect if URL found.
		if ( isset( $item->source ) ) {
			$this->sync_redirect( $item->source, array( 'redirect_id' => null ) );
		}
	}

	/**
	 * Link or unlink a redirect ID to logs with same URL.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $url  URL.
	 * @param array  $data Data to update.
	 *
	 * @return void
	 */
	public function sync_redirect( $url, array $data = array() ) {
		// Can not continue if url is empty.
		if ( empty( $url ) ) {
			return;
		}

		// Update the logs.
		$this->update_multiple( $data, array( 'url' => $url ) );
	}

	/**
	 * Actions after a log is created.
	 *
	 * Newly created logs will have latest no. of hits.
	 * Sync this hit count to all other logs with same URL.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int    $log_id Log ID.
	 * @param object $log    Updated log item.
	 * @param array  $data   Data used for update.
	 *
	 * @return void
	 */
	public function on_log_create( $log_id, $log, $data ) {
		if ( isset( $log->url, $log->hits ) ) {
			// Update hits for all other logs.
			$this->update_multiple(
				array( 'hits' => $log->hits ),
				array( 'url' => $log->url )
			);
		}
	}

	/**
	 * Actions after a log is updated.
	 *
	 * If any of the statuses has been updated, we need to sync
	 * it to all other logs with same url.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int    $log_id Log ID.
	 * @param object $log    Updated log item.
	 * @param array  $data   Data used for update.
	 *
	 * @return void
	 */
	public function on_log_update( $log_id, $log, $data ) {
		// If any of the status values changed.
		if (
			(
				isset( $data['log_status'] ) ||
				isset( $data['email_status'] ) ||
				isset( $data['redirect_status'] )
			) && isset( $log->url )
		) {
			// Sync to all logs.
			$this->update_multiple(
				array(
					'log_status'      => $log->log_status,
					'email_status'    => $log->email_status,
					'redirect_status' => $log->redirect_status,
				),
				array( 'url' => $log->url )
			);
		}

		// If redirect status has been updated, sync to redirect.
		if ( isset( $data['redirect_status'], $log->redirect_id ) ) {
			$redirect = Redirects::instance()->get( $log->redirect_id );
			// Only when status really changed.
			if ( isset( $redirect->status ) && $redirect->status !== $data['redirect_status'] ) {
				// Make sure we don't end up in loop.
				$this->skip_hooks = true;

				Redirects::instance()->update(
					$log->redirect_id,
					array( 'status' => $log->redirect_status )
				);

				$this->skip_hooks = false;
			}
		}
	}

	/**
	 * Modify log data before creation.
	 *
	 * If an existing log found for same URL, use the statuses from
	 * existing log instead of default statuses.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data Data for log creation.
	 *
	 * @return array
	 */
	public function filter_log_data( array $data ) {
		if ( ! empty( $data['url'] ) ) {
			// Get existing log.
			$log = $this->get_by_url( $data['url'] );
			// If status flags found.
			if ( isset( $log->hits, $log->log_status, $log->email_status, $log->redirect_status ) ) {
				$data['hits']            = intval( $log->hits ) + 1;
				$data['log_status']      = $log->log_status;
				$data['email_status']    = $log->email_status;
				$data['redirect_status'] = $log->redirect_status;
			}
		}

		return $data;
	}
}
