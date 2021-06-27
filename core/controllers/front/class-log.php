<?php
/**
 * The plugin menu controller class.
 *
 * This class handles the admin menu functionality for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Menu
 */

namespace DuckDev\Redirect\Controllers\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Models\Logs;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Log extends Action {

	/**
	 * Action type - email.
	 *
	 * @var string $action
	 *
	 * @since 4.0
	 */
	protected $action = 'log';

	/**
	 * Get available redirect types.
	 *
	 * Use `dd404_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function run() {
		// Log data.
		$data = $this->data();

		/**
		 * Action hook to execute before sending email.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_logs_pre_log', $data, $this->request );

		// Send email using wp_mail().
		$success = Logs::instance()->create( $data );

		/**
		 * Action hook to execute after sending email.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_logs_post_log', $data, $this->request, $success );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd404_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	private function data() {
		$data = array(
			'url'     => $this->request->get_url(),
			'date'    => current_time( 'mysql' ),
			'referer' => $this->request->get_referer(),
			'ip'      => $this->request->get_ip(),
			'agent'   => $this->request->get_agent(),
			'status'  => 1,
		);

		/**
		 * Filter hook to enable/disable redirect.
		 *
		 * Other plugins can use this filter to enable
		 * or disable redirect.
		 *
		 * @param bool    $can     Can redirect.
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd404_log_data', $data, $this->request );
	}
}
