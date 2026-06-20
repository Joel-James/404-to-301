<?php
/**
 * Model facade for the 404 logs table.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Queries\Log as LogQuery;
use DuckDev\FourNotFour\Database\Rows\Log as LogRow;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class Logs
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Models
 */
class Logs extends Model {

	/**
	 * Status constants — mirrors the `status` column on the table.
	 *
	 * @since 4.0.0
	 */
	const STATUS_OPEN    = 0;
	const STATUS_IGNORED = 1;
	const STATUS_FIXED   = 2;

	/**
	 * Per-row override value-space (matches the schema). Kept here so
	 * the REST + UI layers can `enum`-validate against a single source.
	 *
	 * @since 4.0.0
	 */
	const OVERRIDE_GLOBAL  = 0;
	const OVERRIDE_ENABLE  = 1;
	const OVERRIDE_DISABLE = 2;

	/**
	 * BerlinDB query class for the logs table.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $query_class = LogQuery::class;

	/**
	 * Find the log row for a normalised URL, if any.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL.
	 *
	 * @return LogRow|null
	 */
	public function get_by_url( string $url ) {
		$hash = Helpers::url_hash( $url );

		$query = new LogQuery(
			array(
				'url_hash' => $hash,
				'number'   => 1,
			)
		);

		$items = (array) $query->items;

		return ! empty( $items ) ? $items[0] : null;
	}

	/**
	 * Record a 404 hit: either insert a new row or bump the `hits`
	 * counter on the existing one.
	 *
	 * @since 4.0.0
	 *
	 * @param array       $data {
	 *     Column => value, must contain at least `url`.
	 *
	 *     @type string $url Raw URL.
	 * }
	 * @param LogRow|null $existing     Pre-fetched row for this URL, or
	 *                                  null when the caller already looked
	 *                                  it up and found nothing. Pair with
	 *                                  `$prefetched = true` to skip the
	 *                                  duplicate SELECT.
	 * @param bool        $prefetched   Whether `$existing` represents a
	 *                                  completed lookup (true) or just a
	 *                                  defaulted "I don't know" (false).
	 *                                  Without this flag we can't tell a
	 *                                  legit "no row exists" null apart
	 *                                  from "caller didn't fetch yet".
	 *
	 * @return int Row id of the inserted/updated log.
	 */
	public function record_hit( array $data, ?LogRow $existing = null, bool $prefetched = false ): int {
		$url = (string) ( $data['url'] ?? '' );

		if ( '' === $url ) {
			return 0;
		}

		if ( ! $prefetched ) {
			$existing = $this->get_by_url( $url );
		}
		$now = current_time( 'mysql', true );

		if ( $existing instanceof LogRow ) {
			$this->bump_existing_hit( $existing, $data, $now );

			return (int) $existing->id;
		}

		$data['url_hash']   = Helpers::url_hash( $url );
		$data['hits']       = (int) ( $data['hits'] ?? 1 );
		$data['status']     = (int) ( $data['status'] ?? self::STATUS_OPEN );
		$data['created_at'] = $now;
		$data['updated_at'] = $now;

		return $this->create( $data );
	}

	/**
	 * Bump the `hits` counter on an existing log row.
	 *
	 * Sidesteps BerlinDB's `update_item()` for this hot, front-end
	 * code path: that helper runs `get_item_raw()` once before the
	 * UPDATE (to diff column values) and once after (to refresh the
	 * row cache), so each 404 hit on a known URL produces two extra
	 * `SELECT * ... WHERE id = X` queries that Query Monitor reports
	 * as duplicates. A direct `$wpdb->update()` collapses the whole
	 * write to a single statement.
	 *
	 * Per-id and last-changed cache entries are invalidated by hand
	 * so the admin Logs list (which reads through BerlinDB) still
	 * reflects the new `hits` value on the next page load.
	 *
	 * @since 4.0.0
	 *
	 * @param LogRow $existing Memoised row whose counter is being bumped.
	 * @param array  $data     Latest request context (`ref`, `ip`, `ua`, `method`).
	 * @param string $now      MySQL-format timestamp for `updated_at`.
	 *
	 * @return void
	 */
	private function bump_existing_hit( LogRow $existing, array $data, string $now ): void {
		global $wpdb;

		$id    = (int) $existing->id;
		$table = $wpdb->prefix . '404_to_301_logs';

		// Use the same fallback-to-current-value pattern as the old
		// BerlinDB update path so contextual columns aren't blanked
		// when the caller doesn't supply them.
		$row = array(
			'hits'       => (int) $existing->hits + 1,
			'updated_at' => $now,
			'ref'        => (string) ( $data['ref'] ?? $existing->ref ),
			'ip'         => (string) ( $data['ip'] ?? $existing->ip ),
			'ua'         => (string) ( $data['ua'] ?? $existing->ua ),
			'method'     => (string) ( $data['method'] ?? $existing->method ),
		);

		// Direct write: BerlinDB's `update_item()` would issue two
		// extra SELECTs around this UPDATE (diff + cache refresh),
		// which Query Monitor flags as duplicates on every 404 hit.
		// Cache invalidation is handled by hand right below.
		$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$table,
			$row,
			array( 'id' => $id ),
			array( '%d', '%s', '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);

		// BerlinDB stores per-row caches under the Query's `cache_group`
		// (declared on {@see LogQuery}); the key is the primary id and
		// the `last_changed` sentinel scopes query-result caches. Both
		// need invalidating so admin listings / `find()` see the bumped
		// values on the next call.
		wp_cache_delete( $id, '404_to_301_logs' );
		wp_cache_set( 'last_changed', microtime(), '404_to_301_logs' );
	}

	/**
	 * Set the status column on a log row.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id     Row id.
	 * @param int $status One of the STATUS_* constants.
	 *
	 * @return bool
	 */
	public function set_status( int $id, int $status ): bool {
		$allowed = array(
			self::STATUS_OPEN,
			self::STATUS_IGNORED,
			self::STATUS_FIXED,
		);

		if ( ! in_array( $status, $allowed, true ) ) {
			return false;
		}

		return $this->update(
			$id,
			array(
				'status'     => $status,
				'updated_at' => current_time( 'mysql', true ),
			)
		);
	}

	/**
	 * Link a log row to a redirect (typically after the admin creates
	 * one from the Logs page).
	 *
	 * @since 4.0.0
	 *
	 * @param int $id          Log row id.
	 * @param int $redirect_id Redirect row id (0 to clear).
	 *
	 * @return bool
	 */
	public function link_redirect( int $id, int $redirect_id ): bool {
		// Status follows the redirect's active state: Fixed when the
		// redirect is live (it handles the 404), Open when it isn't
		// (so the admin knows it still needs attention). Unlinking
		// always reopens the log.
		$status = self::STATUS_OPEN;
		if ( $redirect_id > 0 ) {
			$redirect = \DuckDev\FourNotFour\Models\Redirects::instance()->find( $redirect_id );
			$status   = ( $redirect && 1 === (int) $redirect->is_active )
				? self::STATUS_FIXED
				: self::STATUS_OPEN;
		}

		$data = array(
			'redirect_id' => $redirect_id > 0 ? $redirect_id : null,
			'status'      => $status,
			'updated_at'  => current_time( 'mysql', true ),
		);

		// Reset the per-log redirect override on link. Once a custom
		// redirect exists for the URL, the redirect row's `is_active`
		// owns the on/off decision; leaving a stale DISABLE here would
		// silently re-apply if the admin later deletes the redirect.
		if ( $redirect_id > 0 ) {
			$data['override_redirect'] = self::OVERRIDE_GLOBAL;
		}

		return $this->update( $id, $data );
	}

	/**
	 * Clear the redirect link from every log that references a deleted redirect.
	 *
	 * Called after a redirect row is deleted so orphaned logs don't retain
	 * a stale `redirect_id`. Status is reset to Open so the log resurfaces
	 * for review.
	 *
	 * @since 4.0.1
	 *
	 * @param int $redirect_id Redirect row id that was just deleted.
	 *
	 * @return void
	 */
	public function unlink_redirect( int $redirect_id ): void {
		if ( $redirect_id <= 0 ) {
			return;
		}

		global $wpdb;

		$table = $wpdb->prefix . '404_to_301_logs';
		$now   = current_time( 'mysql', true );

		// Collect affected log ids first so we can bust per-row caches
		// after the UPDATE. BerlinDB serves `find()` from a per-id cache
		// that the `last_changed` sentinel does not invalidate, so a
		// stale row would otherwise come back even after the column has
		// been cleared.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$ids = (array) $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
				"SELECT id FROM `{$table}` WHERE redirect_id = %d",
				$redirect_id
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
				"UPDATE `{$table}` SET redirect_id = NULL, status = %d, updated_at = %s WHERE redirect_id = %d",
				self::STATUS_OPEN,
				$now,
				$redirect_id
			)
		);

		foreach ( $ids as $id ) {
			wp_cache_delete( (int) $id, '404_to_301_logs' );
		}
		wp_cache_set( 'last_changed', microtime(), '404_to_301_logs' );
	}

	/**
	 * Sync the status of every log linked to a redirect when its
	 * `is_active` flag changes.
	 *
	 * Active → Fixed (the redirect handles it).
	 * Inactive → Open (needs attention again).
	 *
	 * @since 4.0.1
	 *
	 * @param int  $redirect_id Redirect row id.
	 * @param bool $is_active   New active state.
	 *
	 * @return void
	 */
	public function sync_status_for_redirect( int $redirect_id, bool $is_active ): void {
		if ( $redirect_id <= 0 ) {
			return;
		}

		global $wpdb;

		$status = $is_active ? self::STATUS_FIXED : self::STATUS_OPEN;
		$table  = $wpdb->prefix . '404_to_301_logs';
		$now    = current_time( 'mysql', true );

		// Collect affected log ids before the UPDATE so we can bust the
		// per-row BerlinDB cache afterwards. Without this, `find()` calls
		// in the same request keep returning the pre-sync status.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$ids = (array) $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
				"SELECT id FROM `{$table}` WHERE redirect_id = %d",
				$redirect_id
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
				"UPDATE `{$table}` SET status = %d, updated_at = %s WHERE redirect_id = %d",
				$status,
				$now,
				$redirect_id
			)
		);

		foreach ( $ids as $id ) {
			wp_cache_delete( (int) $id, '404_to_301_logs' );
		}
		wp_cache_set( 'last_changed', microtime(), '404_to_301_logs' );
	}

	/**
	 * Set the per-row override toggles in one shot.
	 *
	 * Each value must be one of the OVERRIDE_* constants — anything
	 * else is silently coerced to OVERRIDE_GLOBAL so an unexpected
	 * payload never persists a junk value into the table.
	 *
	 * @since 4.0.0
	 *
	 * @param int   $id        Log row id.
	 * @param array $overrides {
	 *     Override values keyed by column.
	 *
	 *     @type int $override_redirect
	 *     @type int $override_email
	 * }
	 *
	 * @return bool
	 */
	public function set_overrides( int $id, array $overrides ): bool {
		$allowed = array( self::OVERRIDE_GLOBAL, self::OVERRIDE_ENABLE, self::OVERRIDE_DISABLE );

		$normalise = static function ( $value ) use ( $allowed ) {
			$value = (int) $value;
			return in_array( $value, $allowed, true ) ? $value : self::OVERRIDE_GLOBAL;
		};

		return $this->update(
			$id,
			array(
				'override_redirect' => $normalise( $overrides['override_redirect'] ?? 0 ),
				'override_email'    => $normalise( $overrides['override_email'] ?? 0 ),
				'updated_at'        => current_time( 'mysql', true ),
			)
		);
	}

	/**
	 * Return aggregate counts for the summary dashboard.
	 *
	 * A single GROUP BY query is cheaper than four COUNT(*) calls.
	 * Counts are keyed by status integer; anything not in the result
	 * defaults to 0 so the caller never has to null-check.
	 *
	 * @since 4.0.1
	 *
	 * @return array{ total: int, open: int, ignored: int, fixed: int, custom: int }
	 */
	public function summary(): array {
		global $wpdb;

		$table = $wpdb->prefix . '404_to_301_logs';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
			"SELECT status, COUNT(*) AS cnt FROM `{$table}` GROUP BY status",
			ARRAY_A
		);

		$counts = array(
			self::STATUS_OPEN    => 0,
			self::STATUS_IGNORED => 0,
			self::STATUS_FIXED   => 0,
		);

		foreach ( (array) $rows as $row ) {
			$status = (int) $row['status'];
			if ( array_key_exists( $status, $counts ) ) {
				$counts[ $status ] = (int) $row['cnt'];
			}
		}

		// Count redirect_id IS NOT NULL separately — this is the ground
		// truth for "has a custom redirect", independent of status.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$custom = (int) $wpdb->get_var(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
			"SELECT COUNT(*) FROM `{$table}` WHERE redirect_id IS NOT NULL"
		);

		return array(
			'total'   => array_sum( $counts ),
			'open'    => $counts[ self::STATUS_OPEN ],
			'ignored' => $counts[ self::STATUS_IGNORED ],
			'fixed'   => $counts[ self::STATUS_FIXED ],
			'custom'  => $custom,
		);
	}

	/**
	 * Truncate the entire logs table.
	 *
	 * Custom redirects live in a separate table and are untouched.
	 * The BerlinDB cache is invalidated so subsequent reads reflect
	 * the empty state without a stale-cache window.
	 *
	 * @since 4.0.1
	 *
	 * @return bool True on success.
	 */
	public function purge_all(): bool {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.SchemaChange
		$result = $wpdb->query( "TRUNCATE TABLE `{$wpdb->prefix}404_to_301_logs`" );

		if ( false !== $result ) {
			wp_cache_set( 'last_changed', microtime(), '404_to_301_logs' );
		}

		return false !== $result;
	}

	/**
	 * Delete rows older than the given number of days.
	 *
	 * @since 4.0.0
	 *
	 * @param int $days Cut-off in days.
	 *
	 * @return int Number of rows deleted.
	 */
	public function prune( int $days ): int {
		if ( $days <= 0 ) {
			return 0;
		}

		$cutoff = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return $this->delete_where(
			array(
				'date_query' => array(
					array(
						'column' => 'created_at',
						'before' => $cutoff,
					),
				),
			)
		);
	}
}
