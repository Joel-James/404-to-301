<?php
/**
 * The action base class.
 *
 * This class is the base for all front end actions.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Action
 */

namespace DuckDev\FourNotFour\Front\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\FourNotFour\Utils\Helpers;
use DuckDev\FourNotFour\Front\Request;

/**
 * Class Action
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front\Actions
 */
abstract class Action {

	/**
	 * Action type (redirect, email and log).
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var string $action
	 */
	protected $action = '';

	/**
	 * Action priority for hook.
	 *
	 * Redirect action should happen at last or else
	 * other actions won't be triggered.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var int $priority
	 */
	protected $priority = 10;

	/**
	 * Current request object.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var Request $request
	 */
	protected $request;

	/**
	 * Initialize the class hooks.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Request object.
	 *
	 * @return void
	 */
	public function __construct( Request $request ) {
		// Set current request.
		$this->request = $request;

		// Process normal request.
		add_action( '404_to_301_request', array( $this, 'process_request' ), $this->priority );
		// Process 404 error request.
		add_action( '404_to_301_404_request', array( $this, 'process_error' ), $this->priority );
	}

	/**
	 * Process the current request action.
	 *
	 * Override this method on action class to perform something
	 * on every page load.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function process_error() {

	}

	/**
	 * Process the 404 error action.
	 *
	 * Override this method on action class to perform something
	 * on every 404 request.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function process_request() {

	}

	/**
	 * Check if current action can be executed.
	 *
	 * Use the filter to disable/enable an action.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	protected function can_proceed() {
		/**
		 * Filter hook to block an action.
		 *
		 * Other plugins can use this filter to stop executing one action.
		 *
		 * @since 4.0
		 *
		 * @param bool    $can     Can proceed.
		 * @param string  $action  Action name.
		 * @param Request $request Request object.
		 */
		return apply_filters( '404_to_301_action_can_proceed', true, $this->action, $this->request );
	}

	/**
	 * Check if the current action is enabled.
	 *
	 * Use the per log status flag and then the global settings
	 * to decide if the action is enabled.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @param string $log_key      Log key for status check.
	 * @param string $settings_key Settings key.
	 *
	 * @return bool
	 */
	protected function is_enabled( $log_key, $settings_key ) {
		// Get status.
		$status = $this->request->get_info( $log_key, 'global' );

		if ( 'global' === $status ) {
			// Get global option.
			$status = duckdev_404_to_301_settings()->get( $settings_key );
		}

		// Get boolean value.
		$enabled = Helpers::get_boolean( $status );

		/**
		 * Filter hook to enable/disable action.
		 *
		 * @since 4.0
		 *
		 * @param bool    $enabled Is enabled.
		 * @param string  $action  Action name.
		 * @param Request $request Request object.
		 */
		return apply_filters( '404_to_301_action_is_enabled', $enabled, $this->action, $this->request );
	}
}
