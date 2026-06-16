<?php
/**
 * Miscellaneous helpers used across the plugin.
 *
 * Small, stateless utilities that don't belong on a particular
 * subsystem: redirect status code catalogue, URL hashing, bot
 * detection, IP packing/unpacking, etc.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Utils;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Helpers
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Utils
 */
class Helpers {

	/**
	 * Canonical catalogue of HTTP status codes the plugin can issue.
	 *
	 * This is the single source of truth for "what redirect types do we
	 * support?" — every other layer derives from it:
	 *
	 *   - the global 404-fallback setting (`redirect_type`) offers the
	 *     {@see Helpers::redirect_status_codes()} `$redirecting_only`
	 *     subset, since a fallback always points at a destination;
	 *   - the per-redirect REST enum offers the full set;
	 *   - the React UI reads the shaped list off the `d404` global
	 *     (localised in {@see \DuckDev\FourNotFour\Admin\Assets}).
	 *
	 * Codes split into two kinds:
	 *
	 *   - **redirecting** (301/302/303/307/308) — issued via
	 *     `wp_safe_redirect()` with a destination.
	 *   - **terminal** (410/451) — no destination; the front controller
	 *     emits the status header and exits. Flagged `terminal => true`.
	 *
	 * The set mirrors the redirect codes other plugins (Redirection,
	 * 301 Redirects) export, so imported rows map across cleanly.
	 *
	 * Filterable so add-ons can introduce further codes (or drop ones
	 * they don't want offered).
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array{label: string, terminal: bool}>
	 */
	public static function redirect_statuses(): array {
		$statuses = array(
			301 => array(
				'label'    => __( '301 — Moved Permanently (SEO)', '404-to-301' ),
				'terminal' => false,
			),
			302 => array(
				'label'    => __( '302 — Found', '404-to-301' ),
				'terminal' => false,
			),
			303 => array(
				'label'    => __( '303 — See Other', '404-to-301' ),
				'terminal' => false,
			),
			307 => array(
				'label'    => __( '307 — Temporary Redirect', '404-to-301' ),
				'terminal' => false,
			),
			308 => array(
				'label'    => __( '308 — Permanent Redirect', '404-to-301' ),
				'terminal' => false,
			),
			410 => array(
				'label'    => __( '410 — Gone', '404-to-301' ),
				'terminal' => true,
			),
			451 => array(
				'label'    => __( '451 — Unavailable for Legal Reasons', '404-to-301' ),
				'terminal' => true,
			),
		);

		/**
		 * Filter the catalogue of supported HTTP status codes.
		 *
		 * Each entry is keyed by the integer status code and carries a
		 * translated `label` plus a `terminal` flag (true for codes that
		 * end the request without redirecting, eg. 410/451).
		 *
		 * @since 4.0.0
		 *
		 * @param array<int, array{label: string, terminal: bool}> $statuses Code => meta.
		 */
		return (array) apply_filters( '404_to_301_redirect_statuses', $statuses );
	}

	/**
	 * Flat list of supported status codes, derived from
	 * {@see Helpers::redirect_statuses()}.
	 *
	 * Used for REST `enum` validation and setting sanitisation so the
	 * allowed values never drift from the catalogue.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $redirecting_only Exclude terminal codes (410/451).
	 *                               Used by the global fallback, which
	 *                               always redirects to a destination.
	 *
	 * @return array<int, int> List of integer status codes.
	 */
	public static function redirect_status_codes( bool $redirecting_only = false ): array {
		$codes = array();

		foreach ( self::redirect_statuses() as $code => $meta ) {
			if ( $redirecting_only && ! empty( $meta['terminal'] ) ) {
				continue;
			}

			$codes[] = (int) $code;
		}

		return $codes;
	}

	/**
	 * Whether a status code is terminal — emitted as a status header
	 * with no redirect (eg. 410 Gone, 451 Unavailable for Legal
	 * Reasons).
	 *
	 * Reads the `terminal` flag off {@see Helpers::redirect_statuses()},
	 * so add-ons that register new terminal codes via the filter are
	 * honoured at runtime without touching the front controller.
	 *
	 * @since 4.0.0
	 *
	 * @param int $status HTTP status code.
	 *
	 * @return bool
	 */
	public static function is_terminal_status( int $status ): bool {
		$statuses = self::redirect_statuses();

		return ! empty( $statuses[ $status ]['terminal'] );
	}

	/**
	 * Catalogue of global 404-fallback target modes.
	 *
	 * Drives the `redirect_target` setting: what the plugin does with a
	 * 404 that has no matching custom redirect. Core ships three modes:
	 *
	 *   - `link` — redirect to a custom URL.
	 *   - `page` — redirect to an existing page (a 3xx to a 200 page).
	 *   - `none` — do nothing; the theme renders its own 404.
	 *
	 * Add-ons register further modes through the filter. Any value
	 * beyond the core three is treated by the Redirect action as a
	 * "serve in place" disposition: instead of redirecting, it fires
	 * `404_to_301_serve_404` and lets the handler render a response
	 * while keeping the 404 status (see {@see \DuckDev\FourNotFour\Front\Actions\Redirect}).
	 *
	 * @since 4.0.0
	 *
	 * @return array<string, string> Stored value => translated label.
	 */
	public static function redirect_targets(): array {
		$targets = array(
			'link' => __( 'A custom URL', '404-to-301' ),
			'page' => __( 'An existing page', '404-to-301' ),
			'none' => __( 'No redirect', '404-to-301' ),
		);

		/**
		 * Filter the catalogue of global 404-fallback target modes.
		 *
		 * @since 4.0.0
		 *
		 * @param array<string, string> $targets Stored value => label.
		 */
		return (array) apply_filters( '404_to_301_redirect_targets', $targets );
	}

	/**
	 * Normalise a URL/path for hashing and exact-match lookup.
	 *
	 * The 404 logging path and the custom-redirect lookup both need to
	 * recognise `/foo`, `/foo/`, `/foo?utm=1`, `/About` and
	 * `/about%20us` as the same entry as `/about us`. The chain mirrors
	 * the three rules nginx's `try_files` block uses:
	 *
	 *   1. Strip the query string.
	 *   2. Percent-decode the path so `%20`, `%2F`, etc. compare
	 *      equally to their decoded forms.
	 *   3. Strip a trailing slash on anything that isn't the root, and
	 *      lowercase the result so casing doesn't fragment matches.
	 *
	 * The final value is run through the `404_to_301_normalize_url`
	 * filter so power users can plug in stricter (or looser) rules
	 * without forking the helper.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL or path.
	 *
	 * @return string Normalised path (no query string, no trailing slash, lowercased).
	 */
	public static function normalise_url( string $url ): string {
		$raw = $url;
		$url = trim( $url );

		// If a full URL was passed (an admin pasting `https://site.com/old`
		// rather than `/old`), reduce it to just the path so the host /
		// scheme don't fragment the match.
		if ( false !== strpos( $url, '://' ) ) {
			$path = (string) wp_parse_url( $url, PHP_URL_PATH );
			$url  = '' === $path ? '/' : $path;
		}

		// Strip the query string entirely.
		$qpos = strpos( $url, '?' );
		if ( false !== $qpos ) {
			$url = substr( $url, 0, $qpos );
		}

		// Percent-decode so encoded equivalents collapse onto the same
		// canonical form. `rawurldecode()` (not `urldecode()`) leaves
		// `+` alone — path segments only encode spaces as `%20`, so a
		// literal `+` should not be treated as one.
		$url = rawurldecode( $url );

		// Strip a trailing slash on anything that isn't the root.
		if ( strlen( $url ) > 1 && '/' === substr( $url, -1 ) ) {
			$url = rtrim( $url, '/' );
		}

		$normalised = strtolower( $url );

		// Guarantee a leading slash so a source typed as `old-page`
		// matches the request path `/old-page`. `REQUEST_URI` always
		// arrives slash-prefixed; admin-entered sources may not.
		$normalised = '/' . ltrim( $normalised, '/' );

		// Make the path relative to the site's home path. On a
		// subdirectory install (eg. home at `/blog`) the incoming
		// `REQUEST_URI` is `/blog/old-page`, while an admin naturally
		// enters the source as `/old-page`. Stripping the home prefix
		// here — the single chokepoint both storage (`url_hash()`) and
		// lookup (`find_match()`) run through — keeps the two in sync so
		// redirects match regardless of how the source was typed. Root
		// installs have an empty home path, so this is a no-op for them.
		$normalised = self::strip_home_path( $normalised );

		/**
		 * Filter the normalised form of a URL before it's hashed or
		 * compared.
		 *
		 * Return a different string to override the policy — eg.
		 * preserve original casing for case-sensitive sites, or fold
		 * additional URL noise (session ids, language prefixes, …) so
		 * the matcher treats them as the same row.
		 *
		 * Both arguments are passed: `$normalised` is what the helper
		 * would return; `$raw` is the original input. Returning a
		 * non-string falls back to `$normalised`.
		 *
		 * @since 4.0.0
		 *
		 * @param string $normalised Normalised URL.
		 * @param string $raw        Original input as passed in.
		 */
		$filtered = apply_filters( '404_to_301_normalize_url', $normalised, $raw );

		return is_string( $filtered ) ? $filtered : $normalised;
	}

	/**
	 * Strip the site's home path prefix from an already-normalised path.
	 *
	 * Returns the path made relative to `home_url()`'s path component:
	 * `/blog/old-page` becomes `/old-page` when the site lives at
	 * `/blog`. A path that is exactly the home path collapses to `/`.
	 * Root installs (home path empty or `/`) are returned unchanged.
	 *
	 * The home path is lowercased to match the already-lowercased input.
	 *
	 * @since 4.0.0
	 *
	 * @param string $path Normalised, lowercased path.
	 *
	 * @return string
	 */
	private static function strip_home_path( string $path ): string {
		$home = (string) wp_parse_url( home_url(), PHP_URL_PATH );
		$home = strtolower( rtrim( $home, '/' ) );

		if ( '' === $home ) {
			return $path;
		}

		if ( 0 === strpos( $path, $home . '/' ) ) {
			$path = substr( $path, strlen( $home ) );
		} elseif ( $path === $home ) {
			$path = '/';
		}

		return '' === $path ? '/' : $path;
	}

	/**
	 * SHA1 hash of a normalised URL — used as the unique-key column on
	 * the logs and redirects tables.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL or path.
	 *
	 * @return string 40-char hexadecimal hash.
	 */
	public static function url_hash( string $url ): string {
		return sha1( self::normalise_url( $url ) );
	}

	/**
	 * Query-aware SHA1 — keeps the `?query=string` portion as part of
	 * the hash. Used by `query_handling = 'require'` redirect rows so
	 * `/old?promo=summer` and `/old?promo=winter` can coexist as two
	 * distinct exact-match entries.
	 *
	 * The path portion goes through the same lower-case + trailing
	 * slash normalisation as {@see normalise_url()}, but the query
	 * string is kept verbatim — query values can be case-sensitive
	 * (tokens, hashes, etc.).
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL or path.
	 *
	 * @return string 40-char hexadecimal hash.
	 */
	public static function url_hash_with_query( string $url ): string {
		$url = trim( $url );

		$qpos  = strpos( $url, '?' );
		$path  = false === $qpos ? $url : substr( $url, 0, $qpos );
		$query = false === $qpos ? '' : substr( $url, $qpos );

		// Route the path through the same normalisation policy as
		// query-less matching — percent-decode, trailing-slash strip,
		// case-fold, and the `404_to_301_normalize_url` filter. The
		// query string is kept verbatim because values can be
		// case-sensitive (tokens, hashes, …).
		return sha1( self::normalise_url( $path ) . $query );
	}

	/**
	 * Pack an IP address into its binary form for `VARBINARY(16)` storage.
	 *
	 * Uses `inet_pton` so both IPv4 (4 bytes) and IPv6 (16 bytes) fit in
	 * the same column. Returns an empty string when the input isn't a
	 * valid IP, so callers can skip the column write without an extra
	 * check. Unpack the value with {@see Helpers::unpack_ip()}.
	 *
	 * @since 4.0.0
	 *
	 * @param string $ip Dotted-quad IPv4 or colon-hex IPv6.
	 *
	 * @return string Binary packed IP, or '' when invalid.
	 */
	public static function pack_ip( string $ip ): string {
		if ( '' === $ip ) {
			return '';
		}

		$packed = @inet_pton( $ip ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- inet_pton emits a warning on invalid input; we want the warning silenced and the false return.

		return is_string( $packed ) ? $packed : '';
	}

	/**
	 * Convert a packed IP (from {@see Helpers::pack_ip()}) back to its
	 * printable form.
	 *
	 * @since 4.0.0
	 *
	 * @param string $packed Binary packed IP as stored in the DB.
	 *
	 * @return string Printable IP, or '' when the input isn't a valid
	 *                packed address.
	 */
	public static function unpack_ip( string $packed ): string {
		if ( '' === $packed ) {
			return '';
		}

		$ip = @inet_ntop( $packed ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- inet_ntop emits a warning on invalid input; we want the warning silenced and the false return.

		return is_string( $ip ) ? $ip : '';
	}

	/**
	 * Rough heuristic for "is this request from a real human?".
	 *
	 * Used to avoid spamming logs with bot traffic. Filterable so the
	 * heuristic can be replaced wholesale by a real bot-detection
	 * library.
	 *
	 * @since 4.0.0
	 *
	 * @param string $user_agent Raw User-Agent string.
	 *
	 * @return bool True when the request looks like a real browser.
	 */
	public static function is_human( string $user_agent ): bool {
		$is_human = true;

		if ( '' === $user_agent ) {
			$is_human = false;
		} else {
			// Cheap regex check against the obvious bot fingerprints.
			$bot_pattern = '/(bot|crawl|spider|slurp|curl|wget|facebookexternalhit|preview|monitor|fetch|python|java|httpclient)/i';

			if ( preg_match( $bot_pattern, $user_agent ) ) {
				$is_human = false;
			}
		}

		/**
		 * Filter the human-vs-bot determination for a request.
		 *
		 * @since 4.0.0
		 *
		 * @param bool   $is_human   Whether the request looks like a real human.
		 * @param string $user_agent Raw User-Agent string.
		 */
		return (bool) apply_filters( '404_to_301_is_human', $is_human, $user_agent );
	}
}
