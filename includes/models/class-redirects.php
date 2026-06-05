<?php
/**
 * Model facade for the custom redirects table.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Queries\Redirect as RedirectQuery;
use DuckDev\FourNotFour\Database\Rows\Redirect as RedirectRow;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class Redirects
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Models
 */
class Redirects extends Model {

	/**
	 * BerlinDB query class for the redirects table.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $query_class = RedirectQuery::class;

	/**
	 * Find the redirect matching a 404 URL, if any.
	 *
	 * Resolution order:
	 *   1. Exact match on `source_hash` (cheap, unique index).
	 *   2. Prefix match — `match_type = 'prefix'` rows whose `source`
	 *      is a prefix of the requested URL.
	 *   3. Regex match — `match_type = 'regex'` rows whose `source`
	 *      regex hits the requested URL.
	 *
	 * Only `is_active = 1` rows are considered. Returns the first
	 * match found (admin UI ordering decides which one wins when
	 * multiple patterns overlap).
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL.
	 *
	 * @return RedirectRow|null
	 */
	public function find_match( string $url ) {
		$exact = $this->find_exact( $url );
		if ( $exact instanceof RedirectRow ) {
			return $exact;
		}

		$prefix = $this->find_prefix( $url );
		if ( $prefix instanceof RedirectRow ) {
			return $prefix;
		}

		return $this->find_regex( $url );
	}

	/**
	 * Find an exact-match redirect for the URL.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL.
	 *
	 * @return RedirectRow|null
	 */
	public function find_exact( string $url ) {
		$query = new RedirectQuery(
			array(
				'source_hash' => Helpers::url_hash( $url ),
				'match_type'  => 'exact',
				'is_active'   => 1,
				'number'      => 1,
			)
		);

		$items = (array) $query->items;

		return ! empty( $items ) ? $items[0] : null;
	}

	/**
	 * Walk every active prefix rule and return the first one whose
	 * `source` is a prefix of the request URL.
	 *
	 * Done in PHP (not SQL) because the natural SQL form — `WHERE
	 * %s LIKE CONCAT(source, '%')` — isn't expressible via BerlinDB's
	 * query args. Prefix rules are typically a handful per site so an
	 * in-memory scan is fine.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL.
	 *
	 * @return RedirectRow|null
	 */
	public function find_prefix( string $url ) {
		$normalised = Helpers::normalise_url( $url );

		$query = new RedirectQuery(
			array(
				'match_type' => 'prefix',
				'is_active'  => 1,
				'orderby'    => 'source',
				'order'      => 'DESC', // Longer prefixes win.
				// BerlinDB runs `number` through `absint()`, so `-1`
				// silently truncates to `LIMIT 1`. Use `0` for no limit.
				'number'     => 0,
			)
		);

		foreach ( (array) $query->items as $row ) {
			$source = Helpers::normalise_url( (string) $row->source );

			if ( '' !== $source && 0 === strpos( $normalised, $source ) ) {
				return $row;
			}
		}

		return null;
	}

	/**
	 * Walk every active regex rule and return the first one that
	 * matches the request URL.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL.
	 *
	 * @return RedirectRow|null
	 */
	public function find_regex( string $url ) {
		$query = new RedirectQuery(
			array(
				'match_type' => 'regex',
				'is_active'  => 1,
				// See `find_prefix()` — BerlinDB caps `-1` to `LIMIT 1`.
				'number'     => 0,
			)
		);

		foreach ( (array) $query->items as $row ) {
			$pattern = (string) $row->source;
			if ( '' === $pattern ) {
				continue;
			}

			// Allow either bare regex (`^/old/.*$`) or delimited
			// (`#^/old/.*$#i`); wrap when bare.
			if ( '' === $pattern[0] || '/' === $pattern[0] || '#' === $pattern[0] ) {
				$wrapped = $pattern;
			} else {
				$wrapped = '#' . $pattern . '#';
			}

			// Suppress the PCRE warning if the pattern is malformed —
			// returning null below is the right behaviour.
			$matched = @preg_match( $wrapped, $url ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			if ( 1 === $matched ) {
				return $row;
			}
		}

		return null;
	}

	/**
	 * Bump the `hits` counter and `last_hit_at` timestamp on a row.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id Row id.
	 *
	 * @return bool
	 */
	public function record_hit( int $id ): bool {
		$row = $this->find( $id );

		if ( ! $row instanceof RedirectRow ) {
			return false;
		}

		// Hit counters are bumped by the front-controller — not a
		// user-initiated edit. Bypass the audit-stamping `update()`
		// override so a public 404 doesn't masquerade as an admin action.
		return parent::update(
			$id,
			array(
				'hits'        => (int) $row->hits + 1,
				'last_hit_at' => current_time( 'mysql', true ),
			)
		);
	}

	/**
	 * Insert a new redirect, hashing the source as we go.
	 *
	 * @since 4.0.0
	 *
	 * @param array $data Column => value. Must include `source`.
	 *
	 * @return int New row id, or 0 on failure.
	 */
	public function create( array $data ): int {
		$source = (string) ( $data['source'] ?? '' );

		if ( '' === $source ) {
			return 0;
		}

		$data['source_hash'] = Helpers::url_hash( $source );

		$now                = current_time( 'mysql', true );
		$data['created_at'] = $now;
		$data['updated_at'] = $now;

		// Stamp the author when there's a logged-in user. Caller may
		// pre-set the key (eg. WP-CLI passing a `--user` flag) — only
		// fill it in when absent so explicit values are preserved.
		if ( ! array_key_exists( 'modified_by', $data ) ) {
			$user_id = get_current_user_id();
			if ( $user_id > 0 ) {
				$data['modified_by'] = $user_id;
			}
		}

		$id = parent::create( $data );

		if ( $id > 0 ) {
			$this->dispatch_audit( 'created', $id, $data );
		}

		return $id;
	}

	/**
	 * Update a redirect — refreshes the hash if `source` is changed.
	 *
	 * @since 4.0.0
	 *
	 * @param int   $id   Row id.
	 * @param array $data Column => value.
	 *
	 * @return bool
	 */
	public function update( int $id, array $data ): bool {
		if ( isset( $data['source'] ) ) {
			$data['source_hash'] = Helpers::url_hash( (string) $data['source'] );
		}

		$data['updated_at'] = current_time( 'mysql', true );

		// Stamp the author when there's a logged-in user. See
		// `create()` for why explicit caller values are preserved.
		if ( ! array_key_exists( 'modified_by', $data ) ) {
			$user_id = get_current_user_id();
			if ( $user_id > 0 ) {
				$data['modified_by'] = $user_id;
			}
		}

		$result = parent::update( $id, $data );

		if ( $result ) {
			$this->dispatch_audit( 'updated', $id, $data );
		}

		return $result;
	}

	/**
	 * Delete a redirect and emit the audit event.
	 *
	 * @since 4.1.0
	 *
	 * @param int $id Row id.
	 *
	 * @return bool
	 */
	public function delete( int $id ): bool {
		$result = parent::delete( $id );

		if ( $result ) {
			$this->dispatch_audit( 'deleted', $id, array() );
		}

		return $result;
	}

	/**
	 * Fire the audit-trail action for a redirect mutation.
	 *
	 * Addons (security, compliance, activity-log integrations) hook
	 * `404_to_301_redirect_audit` to stream the event elsewhere — eg.
	 * to a SIEM or a third-party activity logger.
	 *
	 * @since 4.1.0
	 *
	 * @param string $action Mutation type: `created`, `updated`, or `deleted`.
	 * @param int    $id     Redirect row id.
	 * @param array  $data   Sanitised data passed to the write. Empty array on delete.
	 *
	 * @return void
	 */
	private function dispatch_audit( string $action, int $id, array $data ): void {
		$user_id = isset( $data['modified_by'] ) ? (int) $data['modified_by'] : get_current_user_id();

		/**
		 * Fires after a redirect row is created, updated, or deleted.
		 *
		 * @since 4.1.0
		 *
		 * @param string $action  One of `created`, `updated`, `deleted`.
		 * @param int    $id      Redirect row id.
		 * @param int    $user_id User responsible for the change, or 0 for
		 *                        non-user contexts (CLI, cron).
		 * @param array  $data    Sanitised payload that was written. Empty on delete.
		 */
		do_action( '404_to_301_redirect_audit', $action, $id, $user_id, $data );
	}
}
