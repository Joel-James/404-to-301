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
class Options extends Schema {

	/**
	 * Columns schema.
	 *
	 * @var   array
	 * @since  4.0.0
	 * @access public
	 */
	public $columns = array(
		'id'              => array(
			'name'     => 'id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'extra'    => 'auto_increment',
			'primary'  => true,
			'sortable' => true,
		),
		'log_id'          => array(
			'name'       => 'log_id',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'redirect_id'     => array(
			'name'       => 'redirect_id',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'redirect_status' => array(
			'name'       => 'redirect_status',
			'type'       => 'enum',
			'searchable' => true,
			'sortable'   => true,
		),
		'log_status'      => array(
			'name'       => 'log_status',
			'type'       => 'enum',
			'searchable' => true,
			'sortable'   => true,
		),
		'email_status'    => array(
			'name'       => 'email_status',
			'type'       => 'enum',
			'searchable' => true,
			'sortable'   => true,
		),
		'created_at'      => array(
			'name'       => 'created_at',
			'type'       => 'datetime',
			'date_query' => true,
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'updated_at'      => array(
			'name'       => 'updated_at',
			'type'       => 'datetime',
			'date_query' => true,
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'updated_by'      => array(
			'name'       => 'updated_by',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
	);
}
