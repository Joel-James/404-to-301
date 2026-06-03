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
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Query;
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
