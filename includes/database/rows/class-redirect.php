<?php
/**
 * Row object for the redirects table.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database\Rows;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Row;

/**
 * Class Redirect
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Database\Rows
 */
class Redirect extends Row {

	/**
	 * Primary key.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $id = 0;

	/**
	 * Source URL or pattern.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $source = '';

	/**
	 * SHA1 of the normalised source — unique index.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $source_hash = '';

	/**
	 * How the source is matched: exact / prefix / regex.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $match_type = 'exact';

	/**
	 * Target kind: 'link', 'page', 'none'.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $target_type = 'link';

	/**
	 * Absolute target URL (when target_type='link').
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $target_url = '';

	/**
	 * Linked WP post/page id (when target_type='page').
	 *
	 * @since 4.0.0
	 * @var int|null
	 */
	public $target_page_id = null;

	/**
	 * HTTP redirect status code.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $redirect_type = 301;

	/**
	 * Active flag.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $is_active = 1;

	/**
	 * Times this redirect has fired.
	 *
	 * @since 4.0.0
	 * @var int
	 */
	public $hits = 0;

	/**
	 * Last hit time (MySQL datetime).
	 *
	 * @since 4.0.0
	 * @var string|null
	 */
	public $last_hit_at = null;

	/**
	 * Admin notes.
	 *
	 * @since 4.0.0
	 * @var string|null
	 */
	public $notes = null;

	/**
	 * Row lifecycle timestamps.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $created_at = '';

	/**
	 * Row lifecycle timestamps.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $updated_at = '';

	/**
	 * Resolve the redirect to an absolute target URL.
	 *
	 * For `link` rows that's the stored URL; for `page` rows we resolve
	 * the linked post permalink at read time.
	 *
	 * @since 4.0.0
	 *
	 * @return string Empty when the redirect has no resolvable target.
	 */
	public function resolve_target(): string {
		switch ( $this->target_type ) {
			case 'link':
				return (string) $this->target_url;

			case 'page':
				if ( null !== $this->target_page_id ) {
					$url = get_permalink( (int) $this->target_page_id );
					return is_string( $url ) ? $url : '';
				}
				return '';

			case 'none':
			default:
				return '';
		}
	}
}
