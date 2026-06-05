<?php
/**
 * Redirect 404 requests to a destination URL.
 *
 * Per-row redirects (from the redirects table) win over the global
 * default. The action fires before `Log` / `Email` are scheduled
 * because a successful redirect terminates the request (via `exit`).
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Front\Actions;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Rows\Redirect as RedirectRow;
use DuckDev\FourNotFour\Front\Request;
use DuckDev\FourNotFour\Models\Redirects;

/**
 * Class Redirect
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front\Actions
 */
class Redirect extends Action {

	/**
	 * Whether this action should fire for the current request.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return bool
	 */
	protected function should_run( Request $request ): bool {
		if ( ! $this->setting( 'redirect_enabled', true ) ) {
			return false;
		}

		if ( $request->is_excluded() ) {
			return false;
		}

		return true;
	}

	/**
	 * Run the redirect (when one is resolved).
	 *
	 * Note: when a redirect fires, this method calls `wp_safe_redirect`
	 * followed by `exit` — no further actions run. The Controller
	 * orders us first for that reason.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return void
	 */
	public function run( Request $request ): void {
		if ( ! $this->should_run( $request ) ) {
			return;
		}

		// Per-row first.
		$row    = $request->redirect();
		$target = '';
		$status = (int) $this->setting( 'redirect_type', '301' );

		if ( $row instanceof RedirectRow ) {
			$target = $row->resolve_target();
			$status = (int) $row->redirect_type;

			// `preserve` rows carry the request's query string across
			// to the destination so tracking params (utm_*, gclid, …)
			// survive the redirect. `require` rows already include the
			// query in the match — preserving it again would double
			// up; `ignore` is the explicit opt-out.
			if ( '' !== $target && 'preserve' === (string) $row->query_handling ) {
				$target = $this->append_request_query( $target, $request->url() );
			}
		} elseif ( $request->is_404() ) {
			// No per-row match: only fall back to the global default
			// when this actually is a 404 (we should never redirect a
			// healthy page just because the action is enabled).
			$target = $this->resolve_global_target();
		}

		/**
		 * Filter the resolved target URL and status code.
		 *
		 * Returning an empty `url` aborts the redirect.
		 *
		 * @since 4.0.0
		 *
		 * @param array{url:string,status:int} $payload Resolved redirect.
		 * @param Request                      $request Current request.
		 */
		$payload = (array) apply_filters(
			'404_to_301_redirect_target',
			array(
				'url'    => $target,
				'status' => $status,
			),
			$request
		);

		$url    = (string) ( $payload['url'] ?? '' );
		$status = (int) ( $payload['status'] ?? 301 );

		if ( '' === $url ) {
			return;
		}

		/**
		 * Fires immediately before the redirect headers are sent.
		 *
		 * @since 4.0.0
		 *
		 * @param string  $url     Target URL.
		 * @param int     $status  HTTP status code.
		 * @param Request $request Current request.
		 */
		do_action( '404_to_301_pre_redirect', $url, $status, $request );

		// Bump the hits counter on the row if we used one, and link
		// the just-written log entry to it so the Logs UI can show
		// which 404 URLs have already been "fixed" by a redirect.
		if ( $row instanceof RedirectRow ) {
			Redirects::instance()->record_hit( (int) $row->id );

			$log = $request->log();
			if ( $log && (int) $log->redirect_id !== (int) $row->id ) {
				\DuckDev\FourNotFour\Models\Logs::instance()->link_redirect(
					(int) $log->id,
					(int) $row->id
				);
			}
		}

		wp_safe_redirect( $url, $status );
		exit;
	}

	/**
	 * Resolve the global default redirect target.
	 *
	 * @since 4.0.0
	 *
	 * @return string Empty when no default is configured.
	 */
	private function resolve_global_target(): string {
		$target_type = (string) $this->setting( 'redirect_target', 'link' );

		switch ( $target_type ) {
			case 'link':
				return (string) $this->setting( 'redirect_link', '' );

			case 'page':
				$page_id = (int) $this->setting( 'redirect_page', 0 );

				if ( $page_id <= 0 ) {
					return '';
				}

				$permalink = get_permalink( $page_id );

				return is_string( $permalink ) ? $permalink : '';

			case 'none':
			default:
				return '';
		}
	}

	/**
	 * Append the request's query string to a destination URL.
	 *
	 * Used by `query_handling = 'preserve'` redirect rows. Merges
	 * sensibly when the destination already carries its own query —
	 * the destination's keys win on collision so an admin's explicit
	 * `?source=campaign` isn't silently overwritten by a request-side
	 * `?source=user`.
	 *
	 * @since 4.0.0
	 *
	 * @param string $target      Destination URL (may already have a query).
	 * @param string $request_url Raw request URI (path + query).
	 *
	 * @return string
	 */
	private function append_request_query( string $target, string $request_url ): string {
		$qpos = strpos( $request_url, '?' );
		if ( false === $qpos ) {
			return $target;
		}

		$incoming = substr( $request_url, $qpos + 1 );
		if ( '' === $incoming ) {
			return $target;
		}

		$args = array();
		wp_parse_str( $incoming, $args );

		if ( empty( $args ) ) {
			return $target;
		}

		// Resolve collisions key-by-key so the destination's own query
		// args (which an admin set deliberately when configuring the
		// row) win over any incoming key of the same name. We pre-fill
		// from the incoming request, then layer the destination's
		// existing args on top.
		$existing = array();
		$dest_q   = strpos( $target, '?' );
		if ( false !== $dest_q ) {
			wp_parse_str( substr( $target, $dest_q + 1 ), $existing );
		}

		$merged = array_merge( $args, $existing );

		return add_query_arg( $merged, $target );
	}
}
