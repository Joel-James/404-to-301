<?php
/**
 * The main actions class.
 *
 * This class starts the main functionality which is handling 404
 * errors on the site.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Cache
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Base;
use DuckDev\Redirect\Models\Request;

/**
 * Class Front
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect
 */
class Front extends Base {

	/**
	 * Current request object.
	 *
	 * @var Request $request
	 * @since  4.0.0
	 * @access private
	 */
	private $request = null;

	/**
	 * Initialize the front end.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public function init() {
		// Setup redirection matches.
		add_action( 'init', array( $this, 'setup_request' ) );
		// Disable guessing.
		add_filter( 'redirect_canonical', array( $this, 'url_guessing' ) );
		// Disable IP if required.
		add_filter( 'dd4t3_request_ip', array( $this, 'hide_ip' ) );
		// Process redirect actions.
		//add_action( 'dd4t3_request_init', '' );
		// Handle 404s.
		add_action( 'template_redirect', array( $this, 'handle_404' ) );
	}

	/**
	 * Setup current request match data.
	 *
	 * Setup current request details and optionally setup
	 * all custom redirect details if required.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public function setup_request() {
		// Don't do anything for WP admin.
		if ( is_admin() ) {
			return;
		}

		// Setup request.
		$request = $this->get_request();

		/**
		 * Action hook to perform plugin redirect actions.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_setup_request', $request );
	}

	/**
	 * Perform 404 actions.
	 *
	 * This method checks if the current request is a 404
	 * and if so, start the actions
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public function handle_404() {
		// Only if 404 and not admin.
		if ( ! is_404() || is_admin() ) {
			return;
		}

		// Error actions.
		$error_actions = array(
			'log'      => 'DuckDev\Redirect\Actions\Log',
			'email'    => 'DuckDev\Redirect\Actions\Email',
			'redirect' => 'DuckDev\Redirect\Actions\Redirect',
		);

		/**
		 * Filter hook to add new error actions to 404 to 301.
		 *
		 * Key should be the name of the action and value
		 * should be the class name. The action class should
		 * extend DuckDev\Redirect\Actions\Action.
		 *
		 * @param array $actions Available actions.
		 *
		 * @since 4.0.0
		 */
		$error_actions = apply_filters( 'dd4t3_error_actions', $error_actions );

		// Perform actions.
		foreach ( $error_actions as $action ) {
			new $action( $this->get_request() );
		}

		/**
		 * Action hook to execute on 404 page.
		 *
		 * All plugin actions will be performed before this hook.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_404_request', $this->get_request() );
	}

	/**
	 * Disable URL guessing if enabled.
	 *
	 * @param bool $guess Current status.
	 *
	 * @since 3.0.4
	 * @return bool
	 */
	public function url_guessing( $guess ) {
		// Check if guessing is disabled.
		$disabled = dd4t3_settings()->get( 'disable_guessing' );

		// Disable only on 404.
		if ( $disabled && is_404() && ! isset( $_GET['p'] ) ) { // phpcs:ignore
			$guess = false;
		}

		return $guess;
	}

	/**
	 * Disable IP logging if required.
	 *
	 * @param string $ip IP address.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function hide_ip( $ip ) {
		// Disable only if asked.
		if ( ! dd4t3_settings()->get( 'ip_logging' ) ) {
			$ip = '';
		}

		return $ip;
	}

	/**
	 * Get current request data.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return array
	 */
	public function get_request() {
		// Setup request.
		if ( null === $this->request ) {
			$this->request = new Request();
		}

		/**
		 * Filter hook to modify current request.
		 *
		 * @param array $request Current request.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_get_request', $this->request );
	}

	/**
	 * Get available actions.
	 *
	 * Use `dd4t3_actions` filter to add new actions that perform
	 * during a 404 is found.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return array
	 */
	private function actions() {
		$actions = array(
			'redirect' => 'DuckDev\Redirect\Actions\Redirect',
		);

		/**
		 * Filter hook to add new actions to 404 to 301.
		 *
		 * Key should be the name of the action and value
		 * should be the class name. The action class should
		 * extend DuckDev\Redirect\Actions\Action.
		 *
		 * @param array $actions Available actions.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_actions', $actions );
	}
}
