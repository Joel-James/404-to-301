<?php
/**
 * Field-level sanitisers shared by Settings and REST schemas.
 *
 * Keeping the field-level rules in one place means the option write
 * path (`Settings::sanitize()`) and the REST schema callbacks
 * (`register_setting()` / `register_rest_route()`) never disagree.
 *
 * Every method takes a raw value and returns a clean value of the
 * expected type. None of them throw — failure modes return the
 * documented fallback so callers don't need try/catch blocks.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Utils;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Sanitizer
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Utils
 */
class Sanitizer {

	/**
	 * Coerce any value to a boolean.
	 *
	 * Accepts `'true'`, `'1'`, `1`, `true` as truthy; everything else
	 * is treated as false.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return bool
	 */
	public static function boolean( $value ): bool {
		if ( is_string( $value ) ) {
			$value = strtolower( trim( $value ) );

			return in_array( $value, array( '1', 'true', 'yes', 'on' ), true );
		}

		return (bool) $value;
	}

	/**
	 * Coerce any value to a non-negative integer.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value Raw value.
	 * @param int   $min   Minimum allowed value (default 0).
	 * @param int   $max   Maximum allowed value (default PHP_INT_MAX).
	 *
	 * @return int
	 */
	public static function integer( $value, int $min = 0, int $max = PHP_INT_MAX ): int {
		$int = (int) $value;

		if ( $int < $min ) {
			$int = $min;
		}

		if ( $int > $max ) {
			$int = $max;
		}

		return $int;
	}

	/**
	 * Sanitise a free-form string field.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string
	 */
	public static function text( $value ): string {
		return sanitize_text_field( (string) $value );
	}

	/**
	 * Sanitise a URL field.
	 *
	 * Returns an empty string when the value is not a valid URL.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string
	 */
	public static function url( $value ): string {
		$url = esc_url_raw( (string) $value );

		return is_string( $url ) ? $url : '';
	}

	/**
	 * Sanitise an email field.
	 *
	 * Returns an empty string when the value is not a valid email.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string
	 */
	public static function email( $value ): string {
		$email = sanitize_email( (string) $value );

		return is_email( $email ) ? $email : '';
	}

	/**
	 * Sanitise a value against a fixed set of allowed values.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed  $value    Raw value.
	 * @param array  $allowed  Whitelist of acceptable values.
	 * @param string $fallback Default returned when the value is not in the whitelist.
	 *
	 * @return string
	 */
	public static function enum( $value, array $allowed, string $fallback = '' ): string {
		$value = (string) $value;

		return in_array( $value, $allowed, true ) ? $value : $fallback;
	}

	/**
	 * Sanitise a list of email addresses (array, comma-separated, or
	 * newline-separated).
	 *
	 * Each candidate is run through {@see sanitize_email()} and then
	 * validated with {@see is_email()}; only valid addresses survive.
	 * Order is preserved, duplicates are dropped.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string[]
	 */
	public static function email_list( $value ): array {
		$candidates = self::string_list( $value );
		$clean      = array();

		foreach ( $candidates as $candidate ) {
			$email = sanitize_email( $candidate );
			if ( '' === $email || ! is_email( $email ) ) {
				continue;
			}
			$clean[] = $email;
		}

		return array_values( array_unique( $clean ) );
	}

	/**
	 * Sanitise a list of strings (one per line / comma-separated / array).
	 *
	 * Accepts either an array or a string containing newline- or
	 * comma-separated values. Returns a clean, de-duplicated list of
	 * trimmed non-empty strings.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string[]
	 */
	public static function string_list( $value ): array {
		if ( is_string( $value ) ) {
			$split = preg_split( '/[\r\n,]+/', $value );
			$value = false === $split ? array() : $split;
		}

		if ( ! is_array( $value ) ) {
			return array();
		}

		$clean = array();

		foreach ( $value as $item ) {
			$item = trim( sanitize_text_field( (string) $item ) );

			if ( '' !== $item ) {
				$clean[] = $item;
			}
		}

		return array_values( array_unique( $clean ) );
	}
}
