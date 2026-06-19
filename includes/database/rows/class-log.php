<?php
/**
 * Row object for the 404 logs table.
 *
 * BerlinDB hydrates one of these per row coming back from the
 * database, then asks for the values via plain property access. The
 * class exists so we can expose typed accessors (eg. unpacking the
 * binary IP) without leaking that conversion into every consumer.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database\Rows;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Row;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class Log
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Database\Rows
 */
class Log extends Row {

	/**
	 * Primary key.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $id = 0;

	/**
	 * 404 URL (raw value as recorded).
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $url = '';

	/**
	 * SHA1 hash of the normalised URL.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $url_hash = '';

	/**
	 * HTTP referer.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $ref = '';

	/**
	 * Packed binary IP. Use {@see Log::ip()} to read the printable form.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $ip = '';

	/**
	 * Visitor User-Agent.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $ua = '';

	/**
	 * HTTP method of the original request.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $method = 'GET';

	/**
	 * Number of times this URL has produced a 404.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $hits = 0;

	/**
	 * Linked redirect id, or null.
	 *
	 * @since 4.0.0
	 * @var int|null
	 */
	public $redirect_id = null;

	/**
	 * Lifecycle status:
	 *   0 = open, 1 = ignored, 2 = fixed, 3 = custom redirect set.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $status = 0;

	/**
	 * Per-row override for the global "redirect on 404" toggle.
	 *
	 * 0 = use global, 1 = force enable, 2 = force disable.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $override_redirect = 0;

	/**
	 * Per-row override for the global "email on 404" alert toggle.
	 *
	 * Same value space as {@see self::$override_redirect}.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $override_email = 0;

	/**
	 * Datetime (MySQL format).
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $created_at = '';

	/**
	 * Datetime (MySQL format).
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $updated_at = '';

	/**
	 * Get the IP address in printable form.
	 *
	 * @since 4.0.0
	 *
	 * @return string Empty when the column is empty / invalid.
	 */
	public function ip(): string {
		return Helpers::unpack_ip( (string) $this->ip );
	}
}
