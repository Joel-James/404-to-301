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
	 * Get the catalogue of HTTP redirect status codes the plugin allows.
	 *
	 * Filterable so addons can introduce, say, a 308 option.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, string> Status code => translated label.
	 */
	public static function redirect_statuses(): array {
		$statuses = array(
			301 => __( '301 — Moved Permanently (SEO)', '404-to-301' ),
			302 => __( '302 — Found', '404-to-301' ),
			307 => __( '307 — Temporary Redirect', '404-to-301' ),
		);

		/**
		 * Filter the catalogue of allowed redirect status codes.
		 *
		 * @since 4.0.0
		 *
		 * @param array<int, string> $statuses Status code => label.
		 */
		return (array) apply_filters( '404_to_301_redirect_statuses', $statuses );
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
	 * The final value is run through the `404_to_301_normalise_url`
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
		$filtered = apply_filters( '404_to_301_normalise_url', $normalised, $raw );

		return is_string( $filtered ) ? $filtered : $normalised;
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
		// case-fold, and the `404_to_301_normalise_url` filter. The
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
