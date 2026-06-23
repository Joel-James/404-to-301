<?php
/**
 * Base model class.
 *
 * A thin facade that owns one BerlinDB Query instance and exposes a
 * small, opinionated CRUD surface to the rest of the plugin. Keeps
 * BerlinDB internals out of every other layer — controllers, REST
 * endpoints, CLI commands and tests all talk to a Model instead of
 * the underlying Query.
 *
 * Subclasses set `$query_class` and add their own typed helpers
 * (`get_by_url()`, `record_hit()` etc.).
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Query;
use DuckDev\FourNotFour\Database\Queries\Base_Query;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Model
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Models
 */
abstract class Model extends Singleton {

	/**
	 * Fully qualified BerlinDB Query class this model wraps.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $query_class = '';

	/**
	 * Lazily-built query instance.
	 *
	 * @since 4.0.0
	 * @var Query|null
	 */
	private $query;

	/**
	 * One-time set up: register the extra-clause translator on
	 * BerlinDB's `{plural}_query_clauses` filter. Called by
	 * {@see Singleton::instance()} the first time the model is built.
	 *
	 * @since 4.0.2
	 *
	 * @return void
	 */
	protected function init(): void {
		$this->register_extra_clauses();
	}

	/**
	 * Register the extra-clause translator for this model's Query class.
	 *
	 * BerlinDB ships `IN`, `NOT IN`, and a `search` LIKE that spreads
	 * across every `searchable` column at once. Two cases it doesn't
	 * cover that the DataViews filter UI relies on:
	 *
	 *   - per-column LIKE (`contains` / `notContains` / `startsWith`);
	 *   - numeric comparisons (`<`, `>`, `<=`, `>=`, `BETWEEN`) on a
	 *     concrete column. BerlinDB's `compare_query` arg exists but
	 *     routes through `WP_Meta_Query`, which short-circuits when
	 *     the item type has no metadata sidecar — ours don't.
	 *
	 * We translate both via two custom query args:
	 *
	 *     like_query:  [ [ 'column' => 'url',  'value' => 'foo', 'compare' => 'contains' ], … ]
	 *     range_query: [ [ 'column' => 'hits', 'value' => 5,     'compare' => '>='      ], … ]
	 *
	 * @since 4.0.2
	 *
	 * @return void
	 */
	private function register_extra_clauses(): void {
		$query_class = $this->query_class;
		if ( '' === $query_class || ! is_subclass_of( $query_class, Base_Query::class ) ) {
			return;
		}

		// Pull the plural via a throwaway instance — BerlinDB stores
		// `item_name_plural` on the class but as a protected property,
		// and instantiating the Query without args runs no SQL.
		$probe  = new $query_class();
		$plural = $probe->get_item_name_plural();
		if ( '' === $plural ) {
			return;
		}

		$hook = "{$plural}_query_clauses";
		// The WP test framework backs up and restores `$wp_filter`
		// between tests, so a filter registered once from a singleton
		// `init()` vanishes after the first test that triggered it.
		// `has_filter()` makes the registration idempotent.
		if ( false !== has_filter( $hook, array( __CLASS__, 'translate_extra_clauses' ) ) ) {
			return;
		}

		add_filter( $hook, array( __CLASS__, 'translate_extra_clauses' ), 10, 2 );
	}

	/**
	 * BerlinDB `{plural}_query_clauses` callback. Translates our
	 * custom `like_query` and `range_query` args into raw WHERE
	 * fragments and ANDs them into the existing clause string.
	 *
	 * Public so {@see has_filter()} can match it across instances —
	 * a closure would force us to wire bookkeeping to dedupe, while
	 * a named method gives WP a stable identity to compare against.
	 *
	 * @since 4.0.2
	 *
	 * @param array $clauses BerlinDB clause pieces.
	 * @param mixed $query   The current BerlinDB Query instance.
	 *
	 * @return array
	 */
	public static function translate_extra_clauses( $clauses, $query ): array {
		$clauses = self::apply_like_query( (array) $clauses, $query );
		$clauses = self::apply_range_query( $clauses, $query );
		return $clauses;
	}

	/**
	 * Build the LIKE clauses for a BerlinDB Query and append them to
	 * the WHERE clause it already has.
	 *
	 * @since 4.0.2
	 *
	 * @param array $clauses BerlinDB clause pieces.
	 * @param mixed $query   The current BerlinDB Query instance.
	 *
	 * @return array Mutated clause pieces.
	 */
	private static function apply_like_query( array $clauses, $query ): array {
		if ( ! $query instanceof Base_Query ) {
			return $clauses;
		}

		$like_query = $query->query_vars['like_query'] ?? null;
		if ( empty( $like_query ) || ! is_array( $like_query ) ) {
			return $clauses;
		}

		global $wpdb;

		$alias  = $query->get_table_alias();
		$prefix = '' !== $alias ? "{$alias}." : '';
		$pieces = array();

		foreach ( $like_query as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			$column  = isset( $entry['column'] ) ? (string) $entry['column'] : '';
			$value   = isset( $entry['value'] ) ? (string) $entry['value'] : '';
			$compare = isset( $entry['compare'] ) ? (string) $entry['compare'] : 'contains';

			if ( '' === $column || '' === $value ) {
				continue;
			}
			// Column names come from a server-side allowlist (see
			// {@see Filter_Mapper}), but belt-and-braces: only allow
			// `[A-Za-z0-9_]` so a malformed allowlist can't smuggle
			// SQL through the column identifier.
			if ( ! preg_match( '/^[A-Za-z0-9_]+$/', $column ) ) {
				continue;
			}

			$escaped = $wpdb->esc_like( $value );
			switch ( $compare ) {
				case 'starts_with':
					$pattern = $escaped . '%';
					$op      = 'LIKE';
					break;
				case 'not_contains':
					$pattern = '%' . $escaped . '%';
					$op      = 'NOT LIKE';
					break;
				case 'contains':
				default:
					$pattern = '%' . $escaped . '%';
					$op      = 'LIKE';
					break;
			}

			// `$prefix` is the table alias declared on the Query class;
			// `$column` is regex-validated above; `$op` comes from the
			// closed switch directly above — all three are server-side
			// constants, not request input. The user-controlled
			// `$pattern` is bound via `%s`.
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- See note above.
			$pieces[] = $wpdb->prepare( "{$prefix}{$column} {$op} %s", $pattern );
		}

		if ( empty( $pieces ) ) {
			return $clauses;
		}

		$where            = isset( $clauses['where'] ) ? (string) $clauses['where'] : '';
		$extra            = implode( ' AND ', $pieces );
		$clauses['where'] = '' === $where ? $extra : "{$where} AND {$extra}";

		return $clauses;
	}

	/**
	 * Build the numeric-range clauses for a BerlinDB Query and append
	 * them to the WHERE clause it already has.
	 *
	 * @since 4.0.2
	 *
	 * @param array $clauses BerlinDB clause pieces.
	 * @param mixed $query   The current BerlinDB Query instance.
	 *
	 * @return array Mutated clause pieces.
	 */
	private static function apply_range_query( array $clauses, $query ): array {
		if ( ! $query instanceof Base_Query ) {
			return $clauses;
		}

		$range_query = $query->query_vars['range_query'] ?? null;
		if ( empty( $range_query ) || ! is_array( $range_query ) ) {
			return $clauses;
		}

		global $wpdb;

		$alias       = $query->get_table_alias();
		$prefix      = '' !== $alias ? "{$alias}." : '';
		$allowed_ops = array( '<', '<=', '>', '>=', '=', '!=', 'BETWEEN' );
		$pieces      = array();

		foreach ( $range_query as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			$column  = isset( $entry['column'] ) ? (string) $entry['column'] : '';
			$value   = $entry['value'] ?? null;
			$compare = isset( $entry['compare'] ) ? (string) $entry['compare'] : '=';

			if ( '' === $column || ! preg_match( '/^[A-Za-z0-9_]+$/', $column ) ) {
				continue;
			}
			if ( ! in_array( $compare, $allowed_ops, true ) ) {
				continue;
			}

			if ( 'BETWEEN' === $compare ) {
				if ( ! is_array( $value ) || 2 !== count( $value ) || ! is_numeric( $value[0] ) || ! is_numeric( $value[1] ) ) {
					continue;
				}
				// `$prefix` is the table alias; `$column` is regex-validated.
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- See note above.
				$pieces[] = $wpdb->prepare( "{$prefix}{$column} BETWEEN %f AND %f", (float) $value[0], (float) $value[1] );
				continue;
			}

			if ( ! is_numeric( $value ) ) {
				continue;
			}
			// `$prefix` is the table alias; `$column` and `$compare` are
			// validated against an allowlist above.
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- See note above.
			$pieces[] = $wpdb->prepare( "{$prefix}{$column} {$compare} %f", (float) $value );
		}

		if ( empty( $pieces ) ) {
			return $clauses;
		}

		$where            = isset( $clauses['where'] ) ? (string) $clauses['where'] : '';
		$extra            = implode( ' AND ', $pieces );
		$clauses['where'] = '' === $where ? $extra : "{$where} AND {$extra}";

		return $clauses;
	}

	/**
	 * Get the BerlinDB query instance, building it on first access.
	 *
	 * @since 4.0.0
	 *
	 * @return Query
	 */
	protected function query(): Query {
		if ( null === $this->query ) {
			$class       = $this->query_class;
			$this->query = new $class();
		}

		return $this->query;
	}

	/**
	 * Fetch a single row by primary key.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id Row id.
	 *
	 * @return object|null Row instance, or null when not found.
	 */
	public function find( int $id ) {
		if ( $id <= 0 ) {
			return null;
		}

		$row = $this->query()->get_item( $id );

		return $row ? $row : null;
	}

	/**
	 * Run a paginated query.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args BerlinDB query args (where clauses, order, paging).
	 *
	 * @return array{items: object[], total: int}
	 */
	public function paginate( array $args = array() ): array {
		// Re-arm the `{plural}_query_clauses` filter — `init()` runs
		// once per process via the singleton, but the WP test framework
		// resets `$wp_filter` between tests, so a single registration
		// at construction time doesn't survive. `register_extra_clauses()`
		// is idempotent via `has_filter()`.
		$this->register_extra_clauses();

		$args = wp_parse_args(
			$args,
			array(
				'number'  => 20,
				'offset'  => 0,
				'orderby' => 'id',
				'order'   => 'DESC',
			)
		);

		// Force found-rows so the total comes back without a second
		// query. BerlinDB respects this flag.
		$args['no_found_rows'] = false;

		$query = new $this->query_class( $args );

		return array(
			'items' => (array) $query->items,
			'total' => (int) $query->found_items,
		);
	}

	/**
	 * Insert a new row.
	 *
	 * @since 4.0.0
	 *
	 * @param array $data Column => value.
	 *
	 * @return int New row id, or 0 on failure.
	 */
	public function create( array $data ): int {
		$id = $this->query()->add_item( $data );

		return (int) ( $id ? $id : 0 );
	}

	/**
	 * Update an existing row.
	 *
	 * @since 4.0.0
	 *
	 * @param int   $id   Row id.
	 * @param array $data Column => value.
	 *
	 * @return bool
	 */
	public function update( int $id, array $data ): bool {
		if ( $id <= 0 ) {
			return false;
		}

		return (bool) $this->query()->update_item( $id, $data );
	}

	/**
	 * Delete a row.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id Row id.
	 *
	 * @return bool
	 */
	public function delete( int $id ): bool {
		if ( $id <= 0 ) {
			return false;
		}

		return (bool) $this->query()->delete_item( $id );
	}

	/**
	 * Delete every row that matches the given query args.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args BerlinDB query args.
	 *
	 * @return int Number of rows deleted.
	 */
	public function delete_where( array $args ): int {
		$args['fields'] = 'ids';
		// BerlinDB runs the `number` arg through `absint()` before
		// building the LIMIT clause, so `-1` becomes `LIMIT 1`. Pass
		// `0` to skip the LIMIT entirely (per the SDK docs).
		$args['number'] = 0;

		$query = new $this->query_class( $args );
		$ids   = (array) $query->items;
		$count = 0;

		foreach ( $ids as $id ) {
			if ( $this->delete( (int) $id ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Whether the table has any rows.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function has_items(): bool {
		$query = new $this->query_class(
			array(
				'number' => 1,
				'fields' => 'ids',
			)
		);

		return ! empty( $query->items );
	}
}
