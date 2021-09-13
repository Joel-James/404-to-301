<?php
/**
 * The action base class.
 *
 * This class is the base for all 404 actions.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Action
 */

namespace DuckDev\Redirect\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Helpers;
use DuckDev\Redirect\Models\Request;

/**
 * Class Action
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Actions
 */
abstract class Action {

	/**
	 * Action type (redirect, email and log).
	 *
	 * @var string $action
	 * @since  4.0.0
	 * @access protected
	 */
	protected $action = '';

	/**
	 * Current request object.
	 *
	 * @var Request $request
	 * @since  4.0.0
	 * @access protected
	 */
	protected $request = null;

	/**
	 * Initialize the class hooks.
	 *
	 * @param Request $request Request object.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	public function __construct( Request $request ) {
		$this->request = $request;

		// Process the action if allowed.
		if ( $this->can_proceed() ) {
			$this->process();
		}
	}

	/**
	 * Process the current action.
	 *
	 * Actions should implement this method and perform the
	 * action using the request data available.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @return void
	 */
	abstract protected function process();

	/**
	 * Check if current action can be executed.
	 *
	 * Check settings and see if the action is enabled.
	 * Do extra checks before executing the action.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @return bool
	 */
	protected function can_proceed() {
		// Check if enabled.
		$can = $this->enabled();

		/**
		 * Filter hook to block an action.
		 *
		 * Other plugins can use this filter to stop executing one action.
		 *
		 * @param bool    $can     Can proceed.
		 * @param string  $action  Action name.
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		return apply_filters(
			'dd4t3_action_can_proceed',
			$can,
			$this->action,
			$this->request
		);
	}

	/**
	 * Check if the current action is enabled.
	 *
	 * Use the per log status flag and then the global settings
	 * to decide if the action is enabled.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @return bool
	 */
	protected function enabled() {
		// Get status.
		$status = $this->request->get_config(
			"{$this->action}_status",
			'global'
		);

		if ( 'global' === $status ) {
			// Get global option.
			$status = dd4t3_settings()->get( "{$this->action}_enabled" );
		}

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
		return apply_filters(
			'dd4t3_action_enabled',
			Helpers::get_boolean( $status ),
			$this->action,
			$this->request
		);
	}
}
