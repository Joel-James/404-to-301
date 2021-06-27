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

namespace DuckDev\Redirect\Controllers\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Controller;

/**
 * Class Main
 *
 * @package DuckDev\Redirect\Controllers
 */
class Main extends Controller {

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
}
