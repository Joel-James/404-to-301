<?php
/**
 * The request object that travels through the front-end action chain.
 *
 * Wraps the current HTTP request (URL, headers, IP, UA, method) and
 * the two lookups that depend on it: the matching custom redirect and
 * the matching error log. Built once per request by the Controller
 * and passed into every Actionable so the actions don't need to peek
 * at `$_SERVER` themselves.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Rows\Log as LogRow;
use DuckDev\FourNotFour\Database\Rows\Redirect as RedirectRow;
use DuckDev\FourNotFour\Models\Logs;
use DuckDev\FourNotFour\Models\Redirects;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class Request
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front
 */
class Request {

	/**
	 * Memoised lookup of the matching custom redirect.
	 *
	 * @since 4.0.0
	 * @var RedirectRow|null|false False until first lookup.
	 */
	private $redirect = false;

	/**
	 * Memoised lookup of the matching log row.
	 *
	 * @since 4.0.0
	 * @var LogRow|null|false False until first lookup.
	 */
	private $log = false;

	/**
	 * Lazily-built header map.
	 *
	 * @since 4.0.0
	 * @var array<string, string>|null
	 */
	private $headers;

	/**
	 * HTTP method of the current request, uppercase.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function method(): string {
		$method = isset( $_SERVER['REQUEST_METHOD'] )
			? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) )
			: 'GET';

		/**
		 * Filter the resolved request method.
		 *
		 * @since 4.0.0
		 *
		 * @param string  $method  Resolved method.
		 * @param Request $request Current request.
		 */
		return (string) apply_filters( '404_to_301_request_method', $method, $this );
	}

	/**
	 * Referer URL (empty string when missing).
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function referer(): string {
		$ref = isset( $_SERVER['HTTP_REFERER'] )
			? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) )
			: '';

		/** This filter is documented in {@see Request::method()}. */
		return (string) apply_filters( '404_to_301_request_referer', $ref, $this );
	}

	/**
	 * Visitor User-Agent.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function user_agent(): string {
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] )
			? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) )
			: '';

		/** This filter is documented in {@see Request::method()}. */
		return (string) apply_filters( '404_to_301_request_user_agent', $ua, $this );
	}

	/**
	 * Resolved client IP.
	 *
	 * Resolution order (first non-empty wins):
	 *  - `HTTP_X_FORWARDED_FOR` (first hop only)
	 *  - `HTTP_X_REAL_IP`
	 *  - `HTTP_CLIENT_IP`
	 *  - `REMOTE_ADDR`
	 *
	 * When the `mask_ip` setting is on, the resolved IP is replaced
	 * with an empty string before being filtered.
	 *
	 * @since 4.0.0
	 *
	 * @return string Empty when masked / unknown.
	 */
	public function ip(): string {
		$ip = '';

		// Bail to empty when the admin has opted to mask IPs.
		$settings = \DuckDev\FourNotFour\Core::instance()->settings();
		$mask     = $settings ? (bool) $settings->get( 'mask_ip', false ) : false;

		if ( ! $mask ) {
			$ip = $this->detect_ip();
		}

		/** This filter is documented in {@see Request::method()}. */
		return (string) apply_filters( '404_to_301_request_ip', $ip, $this );
	}

	/**
	 * The request URI (path + optional query string).
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function url(): string {
		$url = isset( $_SERVER['REQUEST_URI'] )
			? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
			: '';

		/** This filter is documented in {@see Request::method()}. */
		return (string) apply_filters( '404_to_301_request_url', $url, $this );
	}

	/**
	 * The host header (or SERVER_NAME fallback).
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function host(): string {
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
		} elseif ( isset( $_SERVER['SERVER_NAME'] ) ) {
			$host = sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) );
		} else {
			$host = '';
		}

		/** This filter is documented in {@see Request::method()}. */
		return (string) apply_filters( '404_to_301_request_host', $host, $this );
	}

	/**
	 * Request scheme — `http` or `https`.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function scheme(): string {
		return is_ssl() ? 'https' : 'http';
	}

	/**
	 * Lazily-built map of every incoming HTTP header.
	 *
	 * Names are lowercased and hyphenated so callers don't have to
	 * remember the CGI form (`HTTP_USER_AGENT` -> `user-agent`).
	 *
	 * @since 4.0.0
	 *
	 * @return array<string, string>
	 */
	public function headers(): array {
		if ( null === $this->headers ) {
			$this->headers = $this->collect_headers();
		}

		return $this->headers;
	}

	/**
	 * Whether the current request hit the 404 template.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_404(): bool {
		/**
		 * Filter the 404 check.
		 *
		 * @since 4.0.0
		 *
		 * @param bool    $is_404  Whether `is_404()` returned true.
		 * @param Request $request Current request.
		 */
		return (bool) apply_filters( '404_to_301_request_is_404', is_404(), $this );
	}

	/**
	 * Matching redirect row for this URL (lazy).
	 *
	 * @since 4.0.0
	 *
	 * @return RedirectRow|null
	 */
	public function redirect(): ?RedirectRow {
		if ( false === $this->redirect ) {
			$this->redirect = Redirects::instance()->find_match( $this->url() );
		}

		return $this->redirect ? $this->redirect : null;
	}

	/**
	 * Matching log row for this URL (lazy).
	 *
	 * @since 4.0.0
	 *
	 * @return LogRow|null
	 */
	public function log(): ?LogRow {
		if ( false === $this->log ) {
			$this->log = Logs::instance()->get_by_url( $this->url() );
		}

		return $this->log ? $this->log : null;
	}

	/**
	 * Force a re-lookup of the log row (used after a write).
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function refresh_log(): void {
		$this->log = false;
	}

	/**
	 * Build the lowercased headers map from `$_SERVER`.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string, string>
	 */
	private function collect_headers(): array {
		$headers = array();

		foreach ( $_SERVER as $name => $value ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( 0 === strpos( (string) $name, 'HTTP_' ) ) {
				$header             = strtolower( str_replace( '_', '-', substr( $name, 5 ) ) );
				$headers[ $header ] = sanitize_text_field( wp_unslash( (string) $value ) );
			}
		}

		// `Content-Type` and `Content-Length` aren't prefixed with `HTTP_`.
		if ( isset( $_SERVER['CONTENT_TYPE'] ) ) {
			$headers['content-type'] = sanitize_text_field( wp_unslash( $_SERVER['CONTENT_TYPE'] ) );
		}
		if ( isset( $_SERVER['CONTENT_LENGTH'] ) ) {
			$headers['content-length'] = sanitize_text_field( wp_unslash( $_SERVER['CONTENT_LENGTH'] ) );
		}

		return $headers;
	}

	/**
	 * Resolve the client IP from the usual server variables.
	 *
	 * Returns an empty string when no candidate produces a valid IP.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function detect_ip(): string {
		$candidates = array(
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'HTTP_CLIENT_IP',
			'REMOTE_ADDR',
		);

		foreach ( $candidates as $key ) {
			if ( empty( $_SERVER[ $key ] ) ) {
				continue;
			}

			$raw = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );

			// `X-Forwarded-For` can be a comma-separated chain — pick the first hop.
			if ( false !== strpos( $raw, ',' ) ) {
				$raw = trim( explode( ',', $raw )[0] );
			}

			$valid = filter_var( $raw, FILTER_VALIDATE_IP );

			if ( false !== $valid ) {
				return (string) $valid;
			}
		}

		return '';
	}

	/**
	 * Whether the path matches one of the configured exclude paths.
	 *
	 * Used by Actions to bail before doing any work.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_excluded(): bool {
		$settings = \DuckDev\FourNotFour\Core::instance()->settings();

		if ( ! $settings ) {
			return false;
		}

		$paths = (array) $settings->get( 'exclude_paths', array() );
		if ( empty( $paths ) ) {
			return false;
		}

		$url = Helpers::normalise_url( $this->url() );

		foreach ( $paths as $path ) {
			$path = (string) $path;
			if ( '' === $path ) {
				continue;
			}
			if ( false !== strpos( $url, trim( $path ) ) ) {
				return true;
			}
		}

		return false;
	}
}
