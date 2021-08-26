<?php
/**
 * The log schema class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Schemas
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Database\Schemas;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Schema;

/**
 * Class Logs.
 *
 * @since   4.0.0
 * @extends Schema
 * @package DuckDev\Redirect\Database\Schemas
 */
class Logs extends Schema {

	/**
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @var    string
	 * @access protected
	 * @since  4.0.0
	 */
	protected $prefix = '404_to_301';

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
		'url'             => array(
			'name'       => 'url',
			'type'       => 'mediumtext',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'referrer'        => array(
			'name'       => 'referrer',
			'type'       => 'varchar',
			'length'     => '255',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'ip'              => array(
			'name'       => 'ip',
			'type'       => 'varchar',
			'length'     => '45',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'agent'           => array(
			'name'       => 'agent',
			'type'       => 'varchar',
			'length'     => '255',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'request_method'  => array(
			'name'       => 'method',
			'type'       => 'varchar',
			'length'     => '10',
			'unsigned'   => true,
			'searchable' => true,
			'sortable'   => true,
		),
		'request_data'    => array(
			'name'       => 'request',
			'type'       => 'mediumtext',
			'unsigned'   => true,
			'searchable' => false,
			'sortable'   => false,
		),
		'visits'          => array(
			'name'     => 'visits',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'sortable' => true,
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
		'meta'            => array(
			'name'       => 'meta',
			'type'       => 'mediumtext',
			'unsigned'   => true,
			'searchable' => false,
			'sortable'   => false,
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
