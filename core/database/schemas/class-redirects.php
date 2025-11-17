<?php
/**
 * The log schema class.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Database\Log
 * @subpackage Schema
 */

namespace DuckDev\FourNotFour\Database\Schemas;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Schema;

/**
 * Class Schema.
 *
 * @since   4.0.0
 * @extends Schema
 * @package DuckDev\FourNotFour\Database\Schemas
 */
class Redirects extends Schema {

	/**
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access protected
	 */
	protected $prefix = '404_to_301';

	/**
	 * Columns schema.
	 *
	 * @since  4.0.0
	 * @access public
	 * @var   array
	 */
	public $columns = array(
		'redirect_id' => array(
			'name'     => 'redirect_id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'extra'    => 'auto_increment',
			'primary'  => true,
			'sortable' => true,
		),
		'source'      => array(
			'name'       => 'source',
			'type'       => 'mediumtext',
			'searchable' => true,
			'sortable'   => true,
			'default'    => '',
		),
		'destination' => array(
			'name'       => 'destination',
			'type'       => 'mediumtext',
			'searchable' => true,
			'sortable'   => true,
			'default'    => '',
		),
		'type'        => array(
			'name'       => 'type',
			'type'       => 'int',
			'length'     => '20',
			'unsigned'   => true,
			'sortable'   => true,
			'default'    => '301',
			'allow_null' => true,
		),
		'group'       => array(
			'name'       => 'group',
			'type'       => 'enum',
			'sortable'   => true,
			'default'    => 'custom',
			'allow_null' => true,
		),
		'meta'        => array(
			'name'       => 'meta',
			'type'       => 'mediumtext',
			'default'    => null,
			'allow_null' => true,
		),
		'status'      => array(
			'name'       => 'status',
			'type'       => 'enum',
			'sortable'   => true,
			'default'    => 'enabled',
			'allow_null' => true,
		),
		'hash'        => array(
			'name'       => 'hash',
			'type'       => 'varchar',
			'allow_null' => false,
		),
		'created_at'  => array(
			'name'       => 'created_at',
			'type'       => 'datetime',
			'created'    => true,
			'date_query' => true,
			'unsigned'   => true,
			'sortable'   => true,
			'default'    => '', // Current time.
			'allow_null' => true,
		),
		'updated_at'  => array(
			'name'       => 'updated_at',
			'type'       => 'datetime',
			'modified'   => true,
			'date_query' => true,
			'sortable'   => true,
			'default'    => null,
			'allow_null' => true,
		),
		'created_by'  => array(
			'name'       => 'created_by',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'default'    => 0, // Current user id.
			'allow_null' => true,
		),
		'updated_by'  => array(
			'name'       => 'updated_by',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'default'    => null,
			'allow_null' => true,
		),
	);
}
