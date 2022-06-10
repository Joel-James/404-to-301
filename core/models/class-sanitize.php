<?php
/**
 * The error logs model class.
 *
 * This class handles the database queries for error logs
 * management.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    Endpoint
 * @subpackage Settings
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.

defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Base;

/**
 * Class Tables.
 *
 * @package DuckDev\Redirect\Models
 */
class Validator extends Base {

	/**
	 * IP address maximum length.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	const IP_LENGTH = 45;

	/**
	 * URL maximum length.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	const URL_LENGTH = 2000;

	/**
	 * User agent maximum length.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	const AGENT_LENGTH = 255;

	/**
	 * Referral maximum length.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	const REF_LENGTH = 255;

	/**
	 * Set up the plugin and register all hooks.
	 *
	 * Pro version features and not initialized yet, so do not
	 * execute something on these hooks if you are checking for
	 * Pro version.
	 *
	 * @since 4.0.0
	 *
	 * @param string $field Field name.
	 * @param mixed  $value Field value.
	 * @param string $table Table name.
	 *
	 * @return mixed
	 */
	public function sanitize( $field, $value, $table = '' ) {
		switch ( $field ) {
			case 'id':
			case 'created_at':
			case 'updated_at':
			case 'created_by':
			case 'updated_by':
			case 'log_id':
			case 'redirect_id':
			case 'code':
				$value = (int) $value;
				break;
			case 'url':
			case 'source':
				$value = substr( $value, 0, self::URL_LENGTH );
				break;
			case 'ip':
				$value = substr( $value, 0, self::IP_LENGTH );
				break;
			case 'agent':
				$value = substr( $value, 0, self::AGENT_LENGTH );
				break;
			case 'referrer':
				$value = substr( $value, 0, self::REF_LENGTH );
				break;
			case 'method':
				$value = strtoupper( $value );
				break;
			case 'request':
			case 'options':
				$value = wp_json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK );
				break;
			case 'status':
			case 'log_status':
			case 'email_status':
			case 'redirect_status':
				$value = $this->sanitize_status( $value, $table );
				break;
			case 'destination':
				$value = esc_url_raw( substr( $value, 0, self::URL_LENGTH ) );
				break;
		}

		/**
		 * Filter to modify sanitized field values.
		 *
		 * @since 4.0.0
		 *
		 * @param mixed  $value Field value.
		 * @param string $field Field name.
		 * @param string $table Table name.
		 */
		return apply_filters( 'dd4t3_validator_sanitize', $value, $field, $table );
	}

	/**
	 * Sanitize the options and redirects table status field.
	 *
	 * Allow only defined fields to be used.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @param mixed  $value Field value.
	 * @param string $table Table name.
	 *
	 * @return string
	 */
	private function sanitize_status( $value, $table ) {
		// Allowed fields.
		$options_allowed   = array( 'enabled', 'disabled', 'global' );
		$redirects_allowed = array( 'enabled', 'disabled', 'ignored' );

		// Fallback to default values if not allowed.
		if ( 'options' === $table && ! in_array( $value, $options_allowed, true ) ) {
			$value = 'global';
		} elseif ( 'redirects' === $table && ! in_array( $value, $redirects_allowed, true ) ) {
			$value = 'ignored';
		}

		return $value;
	}
}
