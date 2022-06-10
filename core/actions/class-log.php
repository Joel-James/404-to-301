<?php
/**
 * The error logging class.
 *
 * This class will log the 404 error details in database.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Actions
 * @subpackage Log
 */

namespace DuckDev\Redirect\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Models\Logs;
use DuckDev\Redirect\Models\Request;

/**
 * Class Log
 *
 * @since   4.0.0
 * @extends Action
 * @package DuckDev\Redirect\Actions
 */
class Log extends Action {

	/**
	 * Action type - log.
	 *
	 * @since  4.0.0
	 * @var string $action
	 * @access protected
	 */
	protected $action = 'log';

	/**
	 * Perform 404 log action if required.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function process_error() {
		// Abort action.
		if ( ! $this->can_proceed() ) {
			return;
		}

		// Action not enabled.
		if ( ! $this->is_enabled( 'log_status', 'logs_enabled' ) ) {
			return;
		}

		// We should skip because duplicate disabled.
		if ( ! $this->can_duplicate() ) {
			// Increment visits and bail.
			$this->increment_visits();

			return;
		}

		// Log now.
		$this->log();
	}

	/**
	 * Get the log data and add it to database.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return void
	 */
	private function log() {
		// Get prepared data.
		$data = $this->get_data();

		/**
		 * Action hook to execute before performing a redirect.
		 *
		 * @since 4.0
		 *
		 * @param array   $data    Final log data.
		 * @param Request $request Request object.
		 */
		do_action( 'dd4t3_before_log', $data, $this->request );

		// Log data.
		$success = Logs::instance()->create( $data );

		/**
		 * Action hook to execute after adding a log.
		 *
		 * This will be fired even if the log creation failed.
		 * Please check the $success param to know if the log is created.
		 *
		 * @since 4.0
		 *
		 * @param string  $success Log creation status.
		 * @param int     $data    Added log data.
		 * @param Request $request Request object.
		 */
		do_action( 'dd4t3_after_log', $success, $data, $this->request );
	}

	/**
	 * Get 404 log data from request.
	 *
	 * Sanitization will be performed before query.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return array
	 */
	private function get_data() {
		$data = array(
			'url'            => $this->request->get_url(),
			'referrer'       => $this->request->get_referer(),
			'ip'             => $this->request->get_ip(),
			'agent'          => $this->request->get_agent(),
			'request_method' => $this->request->get_method(),
			'request_data'   => $this->request->get_others(),
		);

		/**
		 * Filter hook to modify log data before saving to db.
		 *
		 * @since 4.0.0
		 *
		 * @param array   $data    Log data.
		 * @param Request $request Request object.
		 */
		return apply_filters( 'dd4t3_log_get_data', $data, $this->request );
	}

	/**
	 * Check if duplicate logging is disabled.
	 *
	 * If duplicate logs are disabled and a log already exist
	 * for the current URL, we should skip.
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	private function can_duplicate() {
		$can = true;

		// Check if a log already exist for current url if duplicate is disabled.
		if ( dd4t3_settings()->get( 'logs_skip_duplicates' ) ) {
			$can = ! $this->request->get_log( 'id' );
		}

		/**
		 * Filter hook to modify 404 duplicate check for logs.
		 *
		 * @since 4.0
		 *
		 * @param bool    $skip    Should skip.
		 * @param Request $request Request object.
		 */
		return apply_filters( 'dd4t3_logs_can_duplicate', $can, $this->request );
	}

	/**
	 * Increment visits count for a log.
	 *
	 * We increment the visits count only when duplicate logs are disabled.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function increment_visits() {
		// We need a log id.
		$id = $this->request->get_log( 'id' );

		if ( ! empty( $id ) ) {
			// Get existing count.
			$visits = (int) $this->request->get_log( 'visits', 0 );

			// Increment visits count.
			$success = Logs::instance()->update_logs(
				array( 'visits' => $visits + 1 ),
				array( 'url' => $this->request->get_log( 'url', 0 ) )
			);

			/**
			 * Action hook executed after incrementing a log's visits count.
			 *
			 * @since 4.0.0
			 *
			 * @param bool    $success Is increment success.
			 * @param Request $request Old visits count.
			 */
			do_action( 'dd4t3_logs_after_increment_visits', $success, $visits );
		}
	}
}
