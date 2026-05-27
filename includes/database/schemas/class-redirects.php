<?php
/**
 * Schema for the custom redirects table.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database\Schemas;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Schema;

/**
 * Class Redirects
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Database\Schemas
 */
class Redirects extends Schema {

	/**
	 * Columns of the custom redirects table.
	 *
	 * @since 4.0.0
	 * @var array<int, array<string, mixed>>
	 */
	public $columns = array(

		// Primary key.
		array(
			'name'     => 'id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'extra'    => 'auto_increment',
			'primary'  => true,
			'sortable' => true,
		),

		// Source URL/pattern to match against. Long VARCHAR; index via
		// the hash column.
		array(
			'name'       => 'source',
			'type'       => 'varchar',
			'length'     => '2048',
			'searchable' => true,
			'sortable'   => true,
		),

		// SHA1 of normalised source — unique key.
		array(
			'name' => 'source_hash',
			'type' => 'char',
			'length' => '40',
		),

		// How `source` is matched: exact / prefix / regex.
		array(
			'name'     => 'match_type',
			'type'     => 'varchar',
			'length'   => '10',
			'default'  => 'exact',
			'sortable' => true,
		),

		// Target kind: 'link' (target_url), 'page' (target_page_id), 'none'.
		array(
			'name'     => 'target_type',
			'type'     => 'varchar',
			'length'   => '10',
			'default'  => 'link',
			'sortable' => true,
		),

		// Absolute target URL (set when target_type='link').
		array(
			'name'       => 'target_url',
			'type'       => 'varchar',
			'length'     => '2048',
			'default'    => '',
			'searchable' => true,
		),

		// Linked WP post/page id (set when target_type='page').
		array(
			'name'       => 'target_page_id',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'allow_null' => true,
			'default'    => null,
		),

		// HTTP status: 301 / 302 / 307.
		array(
			'name'     => 'redirect_type',
			'type'     => 'smallint',
			'length'   => '5',
			'unsigned' => true,
			'default'  => '301',
			'sortable' => true,
		),

		// Active flag — lets admins disable a row without deleting it.
		array(
			'name'     => 'is_active',
			'type'     => 'tinyint',
			'length'   => '3',
			'unsigned' => true,
			'default'  => '1',
			'sortable' => true,
		),

		// Times this redirect has fired.
		array(
			'name'     => 'hits',
			'type'     => 'int',
			'length'   => '11',
			'unsigned' => true,
			'default'  => '0',
			'sortable' => true,
		),

		// Last time this redirect fired.
		array(
			'name'       => 'last_hit_at',
			'type'       => 'datetime',
			'allow_null' => true,
			'default'    => null,
			'date_query' => true,
			'sortable'   => true,
		),

		// Admin notes — free text.
		array(
			'name'       => 'notes',
			'type'       => 'text',
			'allow_null' => true,
			'default'    => null,
			'searchable' => true,
		),

		// Row lifecycle timestamps.
		array(
			'name'       => 'created_at',
			'type'       => 'datetime',
			'default'    => '0000-00-00 00:00:00',
			'date_query' => true,
			'sortable'   => true,
			'created'    => true,
		),
		array(
			'name'       => 'updated_at',
			'type'       => 'datetime',
			'default'    => '0000-00-00 00:00:00',
			'date_query' => true,
			'sortable'   => true,
			'modified'   => true,
		),
	);
}
