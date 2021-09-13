<?php
/**
 * The error logging class.
 *
 * This class will log the 404 error details in database.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
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
	 * Action type - email.
	 *
	 * @var string $action
	 * @access protected
	 * @since  4.0.0
	 */
	protected $action = 'log';

	/**
	 * Process the logging action.
	 *
	 * Save log details to database.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return void
	 */
	protected function process() {
		// Log data.
		$data = $this->get_data();

		/**
		 * Action hook to execute before logging 404.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_logs_pre_log', $data, $this->request );

		// Log data.
		$success = Logs::instance()->create( $data );

		/**
		 * Action hook to execute after logging 404.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_logs_after_log', $data, $this->request, $success );
	}

	/**
	 * Get 404 log data from request.
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
		 * @param bool    $can     Can redirect.
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_log_data', $data, $this->request );
	}
}
