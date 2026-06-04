<?php
/**
 * Front-end request dispatcher.
 *
 * Hooked into `template_redirect`. Builds the {@see Request} once,
 * then hands it to an ordered list of {@see Actionable}s. The default
 * chain is:
 *
 *   1. Redirect — may exit the request if it fires
 *   2. Log
 *   3. Email
 *
 * The chain is filterable via `404_to_301_actions`, so add-ons can
 * inject their own actions (for example, a webhook notifier) without
 * forking the Controller.
 *
 * Also hooks the WordPress `redirect_canonical` filter to honour the
 * "disable URL guessing" setting.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Contracts\Actionable;
use DuckDev\FourNotFour\Core;
use DuckDev\FourNotFour\Front\Actions\Email;
use DuckDev\FourNotFour\Front\Actions\Log;
use DuckDev\FourNotFour\Front\Actions\Redirect;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Controller
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front
 */
class Controller extends Singleton {

	/**
	 * Register hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_filter( 'redirect_canonical', array( $this, 'disable_canonical_guessing' ) );
		add_action( 'template_redirect', array( $this, 'dispatch' ), 1 );
	}

	/**
	 * Build the request, run the action chain.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function dispatch(): void {
		// Cheap bail-outs first.
		if ( defined( 'WP_CLI' ) && \WP_CLI ) {
			return;
		}

		if ( is_admin() ) {
			$settings = Core::instance()->settings();
			$track    = $settings && $settings->get( 'track_admin_404', false );

			if ( ! $track ) {
				return;
			}
		}

		$request = new Request();

		/**
		 * Allow short-circuiting the whole pipeline.
		 *
		 * @since 4.0.0
		 *
		 * @param bool    $proceed Whether to run the action chain.
		 * @param Request $request Current request.
		 */
		if ( ! apply_filters( '404_to_301_should_process', true, $request ) ) {
			return;
		}

		foreach ( $this->actions( $request ) as $action ) {
			if ( $action instanceof Actionable ) {
				$action->run( $request );
			}
		}

		/**
		 * Fires after every action in the chain has run (and the
		 * request hasn't been redirected away).
		 *
		 * @since 4.0.0
		 *
		 * @param Request $request Current request.
		 */
		do_action( '404_to_301_request', $request );

		if ( $request->is_404() ) {
			/**
			 * Fires on every 404 request, after every action has run.
			 *
			 * @since 4.0.0
			 *
			 * @param Request $request Current request.
			 */
			do_action( '404_to_301_404_request', $request );
		}
	}

	/**
	 * Build the ordered action list for the current request.
	 *
	 * Order is deliberate:
	 *
	 *   1. Log    — records the 404 hit and the request context. Runs
	 *               first so every 404 is captured, even if Redirect
	 *               is about to terminate the request with `exit`.
	 *               Without this ordering, every URL with a matching
	 *               redirect (or a global default) would be silently
	 *               redirected and never appear in the Logs table.
	 *   2. Email  — reads the just-written `hits` counter for the
	 *               threshold check.
	 *   3. Redirect — fires last; calls `wp_safe_redirect` + `exit`
	 *               so anything after it would not run.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return Actionable[]
	 */
	private function actions( Request $request ): array {
		$actions = array(
			new Log(),
			new Email(),
			new Redirect(),
		);

		/**
		 * Filter the action chain for this request.
		 *
		 * Add-ons that want to inject custom actions should hook here
		 * and return a modified array. Each element must implement
		 * {@see Actionable}.
		 *
		 * @since 4.0.0
		 *
		 * @param Actionable[] $actions Default action chain.
		 * @param Request      $request Current request.
		 */
		return (array) apply_filters( '404_to_301_actions', $actions, $request );
	}

	/**
	 * Disable WordPress's URL-guessing redirect when the admin opts to.
	 *
	 * `redirect_canonical` is the filter WordPress uses to decide
	 * whether to redirect, say, `/post-typo` to `/post-name`. The
	 * setting is opt-in because some sites prefer the guessing.
	 *
	 * @since 4.0.0
	 *
	 * @param string|bool $guess Current redirect target (false to disable).
	 *
	 * @return string|bool
	 */
	public function disable_canonical_guessing( $guess ) {
		$settings = Core::instance()->settings();

		if ( $settings && $settings->get( 'disable_guessing', true ) && ! isset( $_GET['p'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		return $guess;
	}
}
