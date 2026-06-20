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
		$candidates = $this->candidates_for_url( $url );

		// Resolution order is fixed (exact > prefix > regex) and the
		// per-bucket walks are unchanged from the dedicated `find_*`
		// methods — the single-query fetch above just gets all three
		// buckets in one round-trip instead of three.
		$exact = $this->pick_exact( $url, $candidates['exact'] );
		if ( $exact instanceof RedirectRow ) {
			return $exact;
		}

		$prefix = $this->pick_prefix( $url, $candidates['prefix'] );
		if ( $prefix instanceof RedirectRow ) {
			return $prefix;
		}

		return $this->pick_regex( $url, $candidates['regex'] );
	}

	/**
	 * Fetch every redirect row that could match this URL, in one query.
	 *
	 * The dispatcher walks exact → prefix → regex on every healthy
	 * page view; without consolidation that's three separate SQL
	 * statements (plus a prime). Folding them into a single
	 * `WHERE (exact-hash) OR match_type IN ('prefix','regex')` cuts
	 * cold-cache cost to one query — the same approach the Redirection
	 * plugin uses on its `match_url` column.
	 *
	 * Cached per request URL: the result set varies by the URL's
	 * `source_hash`, so two distinct URLs would otherwise duplicate the
	 * prefix/regex set in their cache entries. wp_cache is ephemeral
	 * without a persistent backend, so the duplication has no lasting
	 * cost; with one (Redis/Memcached) the same-URL warm hit is free.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw request URL.
	 *
	 * @return array{exact:RedirectRow[],prefix:RedirectRow[],regex:RedirectRow[]}
	 */
	private function candidates_for_url( string $url ): array {
		$hash_with    = Helpers::url_hash_with_query( $url );
		$hash_without = Helpers::url_hash( $url );

		return (array) $this->cache()->remember(
			'candidates:' . $hash_with,
			static function () use ( $hash_with, $hash_without ) {
				global $wpdb;

				$table = $wpdb->prefix . '404_to_301_redirects';

				// One scan fetches every candidate: any active `exact`
				// row whose hash matches either the query-aware or
				// query-stripped form (so a `require` row wins over an
				// `ignore` row for the same path, just like the
				// dedicated `find_exact`), plus every active `prefix`
				// row (walked in PHP — the natural SQL form isn't
				// expressible cheaply) and every active `regex` row
				// (PCRE in PHP). `ORDER BY source DESC` keeps the
				// "longer pattern first" semantics the prefix/regex
				// walks rely on.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
						"SELECT * FROM {$table} WHERE is_active = 1 AND ( ( match_type = 'exact' AND source_hash IN (%s, %s) ) OR match_type = 'prefix' OR match_type = 'regex' ) ORDER BY source DESC",
						$hash_with,
						$hash_without
					)
				);

				$buckets = array(
					'exact'  => array(),
					'prefix' => array(),
					'regex'  => array(),
				);

				if ( is_array( $rows ) ) {
					foreach ( $rows as $row ) {
						$type = (string) ( $row->match_type ?? '' );
						if ( ! isset( $buckets[ $type ] ) ) {
							continue;
						}
						$buckets[ $type ][] = new RedirectRow( $row );
					}
				}

				return $buckets;
			},
			self::CACHE_GROUP
		);
	}

	/**
	 * Pick the exact-match winner from a candidate list.
	 *
	 * Mirrors {@see find_exact()}'s "prefer the query-aware hash" rule:
	 * a `require` row that includes the query string beats an `ignore`
	 * row that only stores the path.
	 *
	 * @since 4.0.0
	 *
	 * @param string        $url        Raw request URL.
	 * @param RedirectRow[] $candidates Active `exact` rows whose hash
	 *                                  matches either form.
	 *
	 * @return RedirectRow|null
	 */
	private function pick_exact( string $url, array $candidates ) {
		if ( empty( $candidates ) ) {
			return null;
		}

		$hash_with    = Helpers::url_hash_with_query( $url );
		$hash_without = Helpers::url_hash( $url );

		$fallback = null;
		foreach ( $candidates as $row ) {
			$hash = (string) ( $row->source_hash ?? '' );
			if ( $hash === $hash_with ) {
				return $row;
			}
			if ( null === $fallback && $hash === $hash_without ) {
				$fallback = $row;
			}
		}

		return $fallback;
	}

	/**
	 * Pick the first prefix rule whose source is a prefix of the URL.
	 *
	 * @since 4.0.0
	 *
	 * @param string        $url        Raw request URL.
	 * @param RedirectRow[] $candidates Active `prefix` rows, longest first.
	 *
	 * @return RedirectRow|null
	 */
	private function pick_prefix( string $url, array $candidates ) {
		if ( empty( $candidates ) ) {
			return null;
		}

		$normalised = Helpers::normalise_url( $url );

		foreach ( $candidates as $row ) {
			$source = Helpers::normalise_url( (string) $row->source );

			if ( '' !== $source && 0 === strpos( $normalised, $source ) ) {
				return $row;
			}
		}

		return null;
	}

	/**
	 * Pick the first regex rule whose pattern matches the URL.
	 *
	 * @since 4.0.0
	 *
	 * @param string        $url        Raw request URL.
	 * @param RedirectRow[] $candidates Active `regex` rows.
	 *
	 * @return RedirectRow|null
	 */
	private function pick_regex( string $url, array $candidates ) {
		if ( empty( $candidates ) ) {
			return null;
		}

		foreach ( $candidates as $row ) {
			$pattern = (string) $row->source;
			if ( '' === $pattern ) {
				continue;
			}

			// Bare regex (`^/old/.*$`) or delimited (`#^/old/.*$#i`);
			// wrap when bare.
			if ( '/' === $pattern[0] || '#' === $pattern[0] ) {
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
				global $wpdb;

				$table = $wpdb->prefix . '404_to_301_redirects';

				// Hand-rolled `SELECT *` instead of going through
				// BerlinDB — its query path issues a SELECT for ids and
				// then a follow-up SELECT to prime the row cache, which
				// doubles the round-trips for a set we always hydrate
				// in full anyway. Prefix/regex rules are a handful per
				// site, so the single scan is cheap and pays no second
				// query. `ORDER BY source DESC` so longer prefixes win.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
						"SELECT * FROM {$table} WHERE match_type = %s AND is_active = 1 ORDER BY source DESC",
						$match_type
					)
				);

				if ( ! is_array( $rows ) ) {
					return array();
				}

				return array_map(
					static function ( $row ) {
						return new RedirectRow( $row );
					},
					$rows
				);
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

		// Keep the autoloaded front-door flag in sync with the table
		// — a delete that empties the active set must be observable on
		// the very next request, even with no persistent object cache.
		$this->refresh_has_active_flag();
	}

	/**
	 * Autoloaded WP option holding the "any active rule?" flag.
	 *
	 * Stored as `'1'` / `'0'` (not bool) so `get_option()` with a
	 * sentinel default can distinguish "never computed" from "computed
	 * and there were no rules" — bool `false` collides with the
	 * not-set return value.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	private const HAS_ACTIVE_OPTION = '404_to_301_has_active';

	/**
	 * Whether the site has at least one active redirect rule.
	 *
	 * Backed by an autoloaded WP option so the read costs zero queries
	 * on a healthy page view — WordPress already loads every autoloaded
	 * option in one bulk fetch per request, regardless of object-cache
	 * state. The option is rewritten by {@see flush_cache()} on every
	 * mutation, so it stays accurate without a per-request DB check.
	 *
	 * Bootstrapped lazily on the first read after a plugin update —
	 * before the option exists we still need to look at the table, but
	 * only once.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	/**
	 * Return aggregate counts for the summary dashboard.
	 *
	 * @since 4.0.1
	 *
	 * @return array{ total: int, active: int, inactive: int, hits: int }
	 */
	public function summary(): array {
		global $wpdb;

		$table = $wpdb->prefix . '404_to_301_redirects';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$row = $wpdb->get_row(
			"SELECT COUNT(*) AS total,
			        SUM(is_active = 1) AS active,
			        SUM(is_active = 0) AS inactive,
			        SUM(hits) AS hits
			 FROM `{$table}`",
			ARRAY_A
		);

		return array(
			'total'    => (int) ( $row['total'] ?? 0 ),
			'active'   => (int) ( $row['active'] ?? 0 ),
			'inactive' => (int) ( $row['inactive'] ?? 0 ),
			'hits'     => (int) ( $row['hits'] ?? 0 ),
		);
	}

	public function has_active(): bool {
		$cached = get_option( self::HAS_ACTIVE_OPTION, 'unset' );

		if ( 'unset' === $cached ) {
			$cached = $this->refresh_has_active_flag();
		}

		return '1' === (string) $cached;
	}

	/**
	 * Recompute the `has_active` flag from the table and persist it.
	 *
	 * Called by {@see flush_cache()} on every mutation, and lazily by
	 * {@see has_active()} the first time it runs on a site that hasn't
	 * computed the flag yet (eg. immediately after a plugin upgrade).
	 *
	 * The single `SELECT 1 ... LIMIT 1` is cheap and only ever runs in
	 * write paths or in that one-time bootstrap.
	 *
	 * @since 4.0.0
	 *
	 * @return string `'1'` if any active row exists, otherwise `'0'`.
	 */
	private function refresh_has_active_flag(): string {
		global $wpdb;

		$table = $wpdb->prefix . '404_to_301_redirects';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$found = $wpdb->get_var( "SELECT 1 FROM {$table} WHERE is_active = 1 LIMIT 1" );

		$value = null === $found ? '0' : '1';

		// `autoload = yes` keeps subsequent reads zero-query — the
		// option rides in on WordPress's per-request alloptions fetch.
		update_option( self::HAS_ACTIVE_OPTION, $value, true );

		return $value;
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
