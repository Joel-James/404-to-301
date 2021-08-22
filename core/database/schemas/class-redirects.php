<?php
/**
 * The log schema class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Log
 * @subpackage Schema
 */

namespace DuckDev\Redirect\Database\Schemas;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Schema;

/**
 * Class Schema.
 *
 * @since   4.0.0
 * @extends Schema
 * @package DuckDev\Redirect\Database\Schemas
 */
class Redirects extends Schema {

	/**
	 * Columns schema.
	 *
	 * @var   array
	 * @since  4.0.0
	 * @access public
	 */
	public $columns = array(
		'id'          => array(
			'name'     => 'id',
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
		),
		'destination' => array(
			'name'       => 'destination',
			'type'       => 'mediumtext',
			'searchable' => true,
			'sortable'   => true,
		),
		'code'        => array(
			'name'       => 'code',
			'type'       => 'mediumtext',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'options'     => array(
			'name'       => 'options',
			'type'       => 'mediumtext',
			'date_query' => true,
			'unsigned'   => true,
			'searchable' => false,
			'sortable'   => false,
		),
		'status'      => array(
			'name'       => 'status',
			'type'       => 'enum',
			'searchable' => true,
			'sortable'   => true,
		),
		'created_at'  => array(
			'name'       => 'created_at',
			'type'       => 'datetime',
			'date_query' => true,
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'updated_at'  => array(
			'name'       => 'updated_at',
			'type'       => 'datetime',
			'date_query' => true,
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'created_by'  => array(
			'name'       => 'created_by',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'updated_by'  => array(
			'name'       => 'updated_by',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
	);
}