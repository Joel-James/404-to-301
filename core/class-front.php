<?php
/**
 * The front end actions class.
 *
 * This class starts the main functionality of the plugin which is on front end of the site.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Front
 */

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\FourNotFour\Utils\Base;
use DuckDev\FourNotFour\Utils\Helpers;
use DuckDev\FourNotFour\Front\Actions;
use DuckDev\FourNotFour\Front\Request;

/**
 * Class Front
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\FourNotFour
 */
class Front extends Base {

	/**
	 * Initialize the front end.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public function init() {
		// Disable guessing.
		add_filter( 'redirect_canonical', array( $this, 'url_guessing' ) );

		// Disable IP if required.
		add_filter( '404_to_301_request_set_ip', array( $this, 'hide_ip' ) );

		// Handle request actions.
		add_action( 'template_redirect', array( $this, 'process_request' ) );
	}

	/**
	 * Perform redirect actions.
	 *
	 * Perform redirect and other actions before template redirect happens.
	 * This method doesn't do anything. We will trigger actions which is used
	 * by the actions.
	 *
	 * - 404_to_301_request : This is fired on all requests.
	 * - 404_to_301_404_request : This action is fired if the current request is 404.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function process_request() {
		// Only if a valid request.
		if ( ! $this->is_valid_request() ) {
			return;
		}

		// Get current request instance.
		$request = $this->get_request();

		// Init actions.
		new Actions\Log( $request );
		new Actions\Email( $request );
		new Actions\Redirect( $request );

		if ( $request->is_404() ) {
			/**
			 * Action hook fired on a 404 request.
			 *
			 * All 404 actions such as logging, emailing should be hooked into this.
			 *
			 * @since 4.0.0
			 *
			 * @param Request $request Request object.
			 */
			do_action( '404_to_301_404_request', $request );
		}

		/**
		 * Action hook fired on all requests.
		 *
		 * This hook can be used to do something on every request such
		 * as checking for custom redirects and do the redirect.
		 *
		 * @since 4.0.0
		 *
		 * @param Request $request Request object.
		 */
		do_action( '404_to_301_request', $request );
	}

	/**
	 * Disable URL guessing if enabled.
	 *
	 * If URL guessing is disabled, we need to stop WordPress from doing
	 * canonical redirects.
	 *
	 * @since  3.0.4
	 * @since  4.0.0 Refactored.
	 * @access public
	 *
	 * @param bool $guess Current status.
	 *
	 * @return bool
	 */
	public function url_guessing( $guess ) {
		// Check if guessing is disabled.
		$disabled = duckdev_404_to_301_settings()->get( 'disable_guessing' );

		if ( $disabled && ! isset( $_GET['p'] ) ) {
			$guess = false;
		}

		return $guess;
	}

	/**
	 * Disable IP logging if required.
	 *
	 * To respect the privacy, we need to stop logging or showing IP
	 * address anywhere on the plugin logs or emails.
	 * Doing this will skip IP address check for the request.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param bool $check IP checking.
	 *
	 * @return bool
	 */
	public function hide_ip( $check ) {
		// Disable only if asked.
		if ( duckdev_404_to_301_settings()->get( 'disable_ip' ) ) {
			return false;
		}

		return $check;
	}

	/**
	 * Get current request data.
	 *
	 * Prepare current request object with all available data.
	 * Use this method to obtain the request object. Do not create
	 * multiple instances by initializing request class everytime.
	 *
	 * - To get current IP : Front::instance()->get_request()->get_ip();
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return Request
	 */
	public function get_request() {
		static $request = null;

		// Setup request.
		if ( null === $request ) {
			$request = new Request();
		}

		/**
		 * Filter hook to modify current request object.
		 *
		 * @since 4.0.0
		 *
		 * @param array $request Current request.
		 */
		return apply_filters( '404_to_301_get_request', $request );
	}

	/**
	 * Check if current request is valid.
	 *
	 * As of now we don't process WP Admin requests.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	private function is_valid_request() {
		// Admin side is an exception.
		$valid = ! is_admin();

		/**
		 * Filter hook to modify valid request check.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $valid Is current request valid.
		 */
		return apply_filters( '404_to_301_is_valid_request', $valid );
	}
}
