<?php
/**
 * Schema for the 404 logs table.
 *
 * Declares the column shape the BerlinDB layer uses to:
 *  - generate the `CREATE TABLE` statement (via `Table::create()`),
 *  - validate values on `Query::add_item()` / `update_item()`,
 *  - drive search / sort / cache-key behaviour on the Query class.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database\Schemas;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Schema;

/**
 * Class Logs
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Database\Schemas
 */
class Logs extends Schema {

	/**
	 * Columns of the 404 logs table.
	 *
	 * The schema is denormalised on purpose — each row carries enough
	 * context (URL, referer, IP, UA) to be useful in the admin UI
	 * without joining the redirects table.
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

		// 404 URL. Stored full-length but indexed via the hash column
		// (MySQL forbids indexing very long VARCHAR columns directly).
		array(
			'name'       => 'url',
			'type'       => 'varchar',
			'length'     => '2048',
			'searchable' => true,
			'sortable'   => true,
		),

		// SHA1 of `Helpers::normalise_url($url)` — unique lookup key.
		array(
			'name'     => 'url_hash',
			'type'     => 'char',
			'length'   => '40',
			'sortable' => false,
		),

		// HTTP referer of the 404 request.
		array(
			'name'       => 'ref',
			'type'       => 'varchar',
			'length'     => '2048',
			'default'    => '',
			'searchable' => true,
		),

		// IP address packed via `inet_pton()` — fits both IPv4 and IPv6.
		array(
			'name'    => 'ip',
			'type'    => 'varbinary',
			'length'  => '16',
			'binary'  => true,
			'default' => '',
		),

		// Visitor User-Agent.
		array(
			'name'       => 'ua',
			'type'       => 'varchar',
			'length'     => '512',
			'default'    => '',
			'searchable' => true,
		),

		// HTTP method of the original request.
		array(
			'name'    => 'method',
			'type'    => 'varchar',
			'length'  => '10',
			'default' => 'GET',
		),

		// Times this URL has produced a 404.
		array(
			'name'     => 'hits',
			'type'     => 'int',
			'length'   => '11',
			'unsigned' => true,
			'default'  => '1',
			'sortable' => true,
		),

		// Linked redirect (if the admin has fixed this URL).
		array(
			'name'       => 'redirect_id',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'allow_null' => true,
			'default'    => null,
			'sortable'   => true,
		),

		// Lifecycle marker:
		// 0 = open, 1 = ignored, 2 = fixed, 3 = custom redirect set.
		array(
			'name'     => 'status',
			'type'     => 'tinyint',
			'length'   => '3',
			'unsigned' => true,
			'default'  => '0',
			'sortable' => true,
		),

		// Per-row override for the global "redirect on 404" toggle.
		// 0 = use global setting, 1 = force enable, 2 = force disable.
		// Mirrors the per-log overrides from the legacy plugin so
		// admins can opt a single 404 path in or out of redirecting.
		array(
			'name'     => 'override_redirect',
			'type'     => 'tinyint',
			'length'   => '3',
			'unsigned' => true,
			'default'  => '0',
		),

		// Per-row override for the global "email on 404" alert toggle.
		// Same value space as `override_redirect`.
		array(
			'name'     => 'override_email',
			'type'     => 'tinyint',
			'length'   => '3',
			'unsigned' => true,
			'default'  => '0',
		),

		// First time we saw the URL.
		array(
			'name'       => 'created_at',
			'type'       => 'datetime',
			'default'    => '0000-00-00 00:00:00',
			'date_query' => true,
			'sortable'   => true,
			'created'    => true,
		),

		// Last time `hits` bumped.
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
