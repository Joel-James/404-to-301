<?php
/**
 * Model facade for the custom redirects table.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\Cache\Cache;
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
	 * Cache prefix for {@see Cache::get_instance()}.
	 *
	 * Scopes every key + group + filter under `404_to_301`, so the
	 * library's per-prefix container can be safely shared with any
	 * other consumer on the site.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	private const CACHE_PREFIX = '404_to_301';

	/**
	 * Cache group for redirect-table lookups.
	 *
	 * One group means a single `flush_group()` on any redirect mutation
	 * invalidates every cached lookup (exact / prefix / regex) at once
	 * — there is no per-key bookkeeping to keep in sync.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	private const CACHE_GROUP = 'redirects';

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
		// `require` rows hash the full URL (path + query) and so are
		// only found via the query-aware hash. Try that first — when
		// the URL has a query string it can match a require row; when
		// it doesn't, the two hashes are identical and the single
		// lookup serves both modes.
		$with_query = Helpers::url_hash_with_query( $url );
		$row        = $this->find_exact_by_hash( $with_query );
		if ( $row instanceof RedirectRow ) {
			return $row;
		}

		// Fall back to the query-stripped hash so `/foo?utm=x` still
		// matches an `ignore` / `preserve` row stored as `/foo`.
		$without_query = Helpers::url_hash( $url );
		if ( $without_query === $with_query ) {
			return null;
		}

		return $this->find_exact_by_hash( $without_query );
	}

	/**
	 * Look up a single active exact-match row by its `source_hash`.
	 *
	 * @since 4.0.0
	 *
	 * @param string $hash SHA1 of either the normalised URL or the URL+query.
	 *
	 * @return RedirectRow|null
	 */
	private function find_exact_by_hash( string $hash ) {
		return $this->cache()->remember(
			'exact:' . $hash,
			static function () use ( $hash ) {
				$query = new RedirectQuery(
					array(
						'source_hash' => $hash,
						'match_type'  => 'exact',
						'is_active'   => 1,
						'number'      => 1,
					)
				);

				$items = (array) $query->items;

				return ! empty( $items ) ? $items[0] : null;
			},
			self::CACHE_GROUP
		);
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
		$rows       = $this->active_rows_by_type( 'prefix' );

		foreach ( $rows as $row ) {
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
		$rows = $this->active_rows_by_type( 'regex' );

		foreach ( $rows as $row ) {
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
	 * Find an existing row that would collide with the given source +
	 * query-handling combination.
	 *
	 * The table has a UNIQUE index on `source_hash`, so two rows with
	 * the same normalised source (in the same query-handling mode)
	 * can't co-exist. Used by the API layer to reject duplicates with
	 * a specific message instead of letting the insert silently fail.
	 *
	 * @since 4.0.0
	 *
	 * @param string $source         Raw source URL/path.
	 * @param string $query_handling `ignore` | `preserve` | `require`.
	 * @param int    $exclude_id     Optional row id to ignore (used by
	 *                               the update path so a row doesn't
	 *                               collide with itself).
	 *
	 * @return RedirectRow|null
	 */
	public function find_by_source( string $source, string $query_handling = 'ignore', int $exclude_id = 0 ) {
		if ( '' === $source ) {
			return null;
		}

		$query = new RedirectQuery(
			array(
				'source_hash' => $this->hash_for_mode( $source, $query_handling ),
				'number'      => 1,
			)
		);

		$items = (array) $query->items;
		$row   = ! empty( $items ) ? $items[0] : null;

		if ( $row instanceof RedirectRow && $exclude_id > 0 && (int) $row->id === $exclude_id ) {
			return null;
		}

		return $row instanceof RedirectRow ? $row : null;
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

		$data['source_hash'] = $this->hash_for_mode( $source, (string) ( $data['query_handling'] ?? 'ignore' ) );

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
		// Either column changing invalidates the hash. When only one
		// of them is in the payload we read the other from the current
		// row so the hash stays consistent with what's stored.
		if ( isset( $data['source'] ) || isset( $data['query_handling'] ) ) {
			$current = $this->find( $id );
			$source  = (string) ( $data['source'] ?? $current->source ?? '' );
			$mode    = (string) ( $data['query_handling'] ?? $current->query_handling ?? 'ignore' );

			if ( '' !== $source ) {
				$data['source_hash'] = $this->hash_for_mode( $source, $mode );
			}
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
	 * @since 4.0.0
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
	 * @since 4.0.0
	 *
	 * @param string $action Mutation type: `created`, `updated`, or `deleted`.
	 * @param int    $id     Redirect row id.
	 * @param array  $data   Sanitised data passed to the write. Empty array on delete.
	 *
	 * @return void
	 */
	/**
	 * Load every active row of a given match type, cached for the
	 * lifetime of the cache group (flushed on any redirect mutation).
	 *
	 * Used by {@see find_prefix()} and {@see find_regex()} — both walk
	 * the entire active set in PHP. The lists are small (a handful of
	 * rules per site) so caching the hydrated rows is cheap and cuts a
	 * SELECT off every 404.
	 *
	 * @since 4.0.0
	 *
	 * @param string $match_type `prefix` or `regex`.
	 *
	 * @return RedirectRow[]
	 */
	private function active_rows_by_type( string $match_type ): array {
		return (array) $this->cache()->remember(
			'active:' . $match_type,
			static function () use ( $match_type ) {
				$query = new RedirectQuery(
					array(
						'match_type' => $match_type,
						'is_active'  => 1,
						'orderby'    => 'source',
						'order'      => 'DESC', // Longer prefixes win.
						// BerlinDB runs `number` through `absint()`,
						// so `-1` silently truncates to `LIMIT 1`.
						// Use `0` for no limit.
						'number'     => 0,
					)
				);

				return (array) $query->items;
			},
			self::CACHE_GROUP
		);
	}

	/**
	 * Drop every cached lookup in the redirects group.
	 *
	 * Hooked from {@see dispatch_audit()} so any create / update /
	 * delete invalidates the exact / prefix / regex caches in one go —
	 * the version-bump implementation in the cache helper means we
	 * don't enumerate keys.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function flush_cache(): void {
		$this->cache()->flush_group( self::CACHE_GROUP );
	}

	/**
	 * Whether the site has at least one active redirect rule.
	 *
	 * Cached in the redirects group (flushed on any mutation) so the
	 * front controller can cheaply decide whether to attempt a per-row
	 * match on healthy (non-404) page views. On a site with no redirects
	 * this stays a single warm cache read — no per-request query.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function has_active(): bool {
		return (bool) $this->cache()->remember(
			'has_active',
			static function () {
				$query = new RedirectQuery(
					array(
						'is_active' => 1,
						'number'    => 1,
						'fields'    => 'ids',
					)
				);

				return ! empty( $query->items );
			},
			self::CACHE_GROUP
		);
	}

	/**
	 * Container for the redirect-table cache group.
	 *
	 * Scoped under `404_to_301` so other consumers on the site can't
	 * collide with our keys, and so the library's
	 * `404_to_301_can_cache` filter can disable caching for debugging.
	 *
	 * @since 4.0.0
	 *
	 * @return Cache
	 */
	private function cache(): Cache {
		return Cache::get_instance( self::CACHE_PREFIX );
	}

	/**
	 * Hash a source URL according to the row's `query_handling` mode.
	 *
	 * `require` rows include the query string in the hash so multiple
	 * rows can share a path with different query requirements; every
	 * other mode hashes the path-only form for case-insensitive,
	 * trailing-slash-tolerant matching.
	 *
	 * @since 4.0.0
	 *
	 * @param string $source The `source` column value.
	 * @param string $mode   `ignore` | `preserve` | `require`.
	 *
	 * @return string 40-char hex SHA1.
	 */
	private function hash_for_mode( string $source, string $mode ): string {
		return 'require' === $mode
			? Helpers::url_hash_with_query( $source )
			: Helpers::url_hash( $source );
	}

	/**
	 * Fire the `404_to_301_redirect_audit` action for a row mutation.
	 *
	 * Centralised so create / update / delete all produce the same shape
	 * — the actor user id is resolved here (falling back to the current
	 * user when the payload doesn't carry an explicit `modified_by`),
	 * and the canonical argument order is locked in one place.
	 *
	 * @since 4.0.0
	 *
	 * @param string $action One of `created`, `updated`, `deleted`.
	 * @param int    $id     Affected redirect row id.
	 * @param array  $data   Sanitised payload that was written. Empty on delete.
	 * @return void
	 */
	private function dispatch_audit( string $action, int $id, array $data ): void {
		// Any mutation invalidates the lookup cache. Done here (not in
		// each create/update/delete) so the single seam stays the only
		// place future write paths need to remember to plumb through.
		$this->flush_cache();

		$user_id = (int) ( $data['modified_by'] ?? get_current_user_id() );

		/**
		 * Fires after a redirect row is created, updated, or deleted.
		 *
		 * @since 4.0.0
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
