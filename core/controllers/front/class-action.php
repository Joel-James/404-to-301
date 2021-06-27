<?php
/**
 * The action base class.
 *
 * This class is the base for all actions.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Action
 */

namespace DuckDev\Redirect\Controllers\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use Exception;
use DuckDev\Redirect\Controllers\Settings;
use DuckDev\Redirect\Utils\Abstracts\Controller;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
abstract class Action extends Controller {

	/**
	 * Action type (redirect, email and log).
	 *
	 * @var string $action
	 *
	 * @since 4.0
	 */
	protected $action = '';

	/**
	 * Current request object.
	 *
	 * @var Request $request
	 *
	 * @since 4.0
	 */
	protected $request = null;

	/**
	 * Initialize the class hooks.
	 *
	 * @throws Exception If action is not defined.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function init() {
		// Make sure the class is valid.
		if ( empty( $this->action ) || ! in_array( $this->action, $this->actions(), true ) ) {
			throw new Exception( get_class( $this ) . ' must have a valid $action property set.' );
		}

		// Perform 404 actions.
		add_action( 'dd404_404_request', array( $this, 'process' ) );
	}

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
	abstract public function run();

	/**
	 * Get available redirect types.
	 *
	 * Use `dd404_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @param Request $request Request object.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function process( Request $request ) {
		// Set request data.
		$this->set_request( $request );
		// Run action if allowed.
		if ( $this->can_run() ) {
			$this->run();
		}
	}

	/**
	 * Get available actions.
	 *
	 * @param Request $request Request object.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_request( Request $request ) {
		/**
		 * Filter hook to add new actions to 404 to 301.
		 *
		 * @param array $actions Available actions.
		 *
		 * @since 4.0
		 */
		$this->request = apply_filters( 'dd404_action_request', $request );
	}

	/**
	 * Check if current action can be executed.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	protected function can_run() {
		// Check if enabled.
		$can = $this->enabled();

		/**
		 * Filter hook to block an action.
		 *
		 * Other plugins can use this filter to block
		 * executing one action.
		 *
		 * @param bool    $can     Can execute.
		 * @param string  $action  Action name.
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd404_action_can_run', $can, $this->action, $this->request );
	}

	/**
	 * Check if the current action is enabled.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	protected function enabled() {
		// Get global option.
		$enabled = Settings::get( 'enabled', $this->action );
		// Get custom settings.
		$config = $this->request->get_config( $this->action, 'global' );

		// Get enabled status.
		$enabled = 'global' === $config ? $enabled : $this->get_boolean( $config );

		/**
		 * Filter hook to enable/disable action.
		 *
		 * Other plugins can use this filter to enable
		 * or disable action.
		 *
		 * @param bool    $enabled Is enabled.
		 * @param string  $action  Action name.
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd404_action_enabled', $enabled, $this->action, $this->request );
	}

	/**
	 * Get available actions.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	private function actions() {
		$actions = array(
			'log',
			'email',
			'redirect',
		);

		/**
		 * Filter hook to add new actions to 404 to 301.
		 *
		 * @param array $actions Available actions.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd404_actions', $actions );
	}
}
