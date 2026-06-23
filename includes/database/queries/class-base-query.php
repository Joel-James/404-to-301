<?php
/**
 * Plugin-local BerlinDB Query base.
 *
 * Adds the small surface this plugin needs on top of BerlinDB's
 * `Query` — currently a public accessor for `$table_alias`, which is
 * protected upstream and needed by the LIKE-query filter registered in
 * {@see \DuckDev\FourNotFour\Models\Model::register_like_query()}.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database\Queries;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Query;

/**
 * Class Base_Query
 *
 * @since   4.0.2
 * @package DuckDev\FourNotFour\Database\Queries
 */
abstract class Base_Query extends Query {

	/**
	 * Public accessor for the table alias used inside BerlinDB SQL.
	 *
	 * BerlinDB keeps `$table_alias` protected, but the LIKE-query
	 * filter callback (registered from outside the Query instance)
	 * needs the alias to qualify its column references the same way
	 * BerlinDB's own `parse_where()` does.
	 *
	 * @since 4.0.2
	 *
	 * @return string
	 */
	public function get_table_alias(): string {
		return (string) $this->table_alias;
	}

	/**
	 * Public accessor for `$item_name_plural`.
	 *
	 * Needed so the Model layer can derive BerlinDB filter hook names
	 * (`{plural}_query_clauses`) without instantiating ReflectionClass.
	 *
	 * @since 4.0.2
	 *
	 * @return string
	 */
	public function get_item_name_plural(): string {
		return (string) $this->item_name_plural;
	}
}
