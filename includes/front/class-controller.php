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
		// Both canonical filters are always added — the mode is read
		// inside the callbacks so a runtime settings change (REST
		// PATCH, WP-CLI, test fixtures) takes effect without
		// re-registration.
		add_filter( 'redirect_canonical', array( $this, 'disable_canonical_guessing' ) );
		add_filter( 'do_redirect_guess_404_permalink', array( $this, 'maybe_block_404_guess' ) );
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
	 * Strict-mode handler for the `redirect_canonical` filter.
	 *
	 * `redirect_canonical` is the top-level filter WP runs on the URL
	 * its canonicalisation function would have redirected to — covers
	 * post-name guessing, trailing slashes, case folding, and the
	 * attachment fallback. Returning `false` short-circuits the whole
	 * function, so we only do that on the `strict` mode. The lighter
	 * mode targets only the 404-guessing portion via
	 * {@see maybe_block_404_guess()}.
	 *
	 * The `?p=` short-circuit is preserved from the v3 behaviour —
	 * `wp_safe_redirect` on a numeric `?p=42` is genuinely useful for
	 * sites that paste old links around.
	 *
	 * @since 4.0.0
	 *
	 * @param string|bool $guess Current redirect target (false to disable).
	 *
	 * @return string|bool
	 */
	public function disable_canonical_guessing( $guess ) {
		if ( isset( $_GET['p'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $guess;
		}

		if ( 'strict' === $this->guessing_mode() ) {
			return false;
		}

		return $guess;
	}

	/**
	 * Light-mode handler for `do_redirect_guess_404_permalink`.
	 *
	 * WP's `redirect_guess_404_permalink()` only walks posts when this
	 * filter returns true. Returning false here keeps the rest of
	 * `redirect_canonical()` (trailing slash, case folding) intact
	 * while killing the "find a similar post by slug" lookup that's
	 * the main source of unexpected redirects.
	 *
	 * Both `light` and `strict` block the guess — strict reaches it
	 * via the top-level filter, but covering both modes here means
	 * the behaviour is consistent even if a plugin re-enables
	 * `redirect_canonical()`.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $should_guess Whether WP intends to attempt the guess.
	 *
	 * @return bool
	 */
	public function maybe_block_404_guess( $should_guess ) {
		if ( isset( $_GET['p'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $should_guess;
		}

		$mode = $this->guessing_mode();

		if ( 'light' === $mode || 'strict' === $mode ) {
			return false;
		}

		return $should_guess;
	}

	/**
	 * Resolve the current `disable_guessing` mode, defending against
	 * stale boolean values that may still be on disk from earlier
	 * pre-release builds.
	 *
	 * @since 4.0.0
	 *
	 * @return string One of `off`, `light`, `strict`.
	 */
	private function guessing_mode(): string {
		$settings = Core::instance()->settings();
		if ( ! $settings ) {
			return 'light';
		}

		$mode = $settings->get( 'disable_guessing', 'light' );

		if ( is_bool( $mode ) ) {
			return $mode ? 'strict' : 'off';
		}

		$mode = is_string( $mode ) ? $mode : 'light';

		return in_array( $mode, array( 'off', 'light', 'strict' ), true ) ? $mode : 'light';
	}
}
