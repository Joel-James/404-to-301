<?php
/**
 * The main actions class.
 *
 * This class starts the main functionality which is handling 404
 * errors on the site.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Main
 */

namespace DuckDev\Redirect\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class Main
 *
 * @package DuckDev\Redirect\Controllers
 */
class Main extends Base {

	/**
	 * Initialize assets functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function init() {
		// Main filter that handles 404.
		add_action( 'template_redirect', array( $this, 'handle_404' ) );
	}

	/**
	 * Perform 404 actions.
	 *
	 * This method checks if the current request is a 404
	 * and if so, execute the custom action hook.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function handle_404() {
		// Only if 404 and not admin.
		if ( ! is_404() || is_admin() ) {
			return;
		}

		// Initialize actions.
		$this->init_actions();

		// Create new request object.
		$request = new Request();

		/**
		 * Action hook to execute on 404 page.
		 *
		 * Use this hook to perform all actions like
		 * logging, email, and redirect.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_404_request', $request );
	}

	/**
	 * Get available actions.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public function actions() {
		$actions = array(
			'log'      => 'DuckDev\Redirect\Controllers\Front\Actions\Log',
			'email'    => 'DuckDev\Redirect\Controllers\Front\Actions\Email',
			'redirect' => 'DuckDev\Redirect\Controllers\Front\Actions\Redirect',
		);

		/**
		 * Filter hook to add new actions to 404 to 301.
		 *
		 * Key should be the name of the action and value
		 * should be the class name. The action class should
		 * extend DuckDev\Redirect\Controllers\Front\Actions\Action.
		 *
		 * @param array $actions Available actions.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd404_actions', $actions );
	}

	/**
	 * Get available actions.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function init_actions() {
		$actions = $this->actions();

		foreach ( $actions as $action => $class ) {
			if (
				class_exists( $class ) // Should be a valid class.
				&& is_subclass_of( $class, 'DuckDev\Redirect\Controllers\Front\Actions\Action' ) // Should extend action class.
			) {
				$class::instance();
			}
		}

		/**
		 * Filter hook to add new actions to 404 to 301.
		 *
		 * @param array $actions Available actions.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_after_actions_init', $actions );
	}
}
