<?php
/**
 * The log schema class.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Database\Schemas
 * @subpackage Logs
 */

namespace RedirectPress\Database\Schemas;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Schema;

/**
 * Class Logs.
 *
 * @since   4.0.0
 * @extends Schema
 * @package RedirectPress\Database\Schemas
 */
class Logs extends Schema {

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
		'log_id'          => array(
			'name'     => 'log_id',
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
			'searchable' => true,
			'sortable'   => true,
		),
		'referrer'        => array(
			'name'       => 'referrer',
			'type'       => 'varchar',
			'length'     => '255',
			'searchable' => true,
			'sortable'   => true,
			'default'    => null,
			'allow_null' => true,
		),
		'ip'              => array(
			'name'       => 'ip',
			'type'       => 'varchar',
			'length'     => '45',
			'searchable' => true,
			'sortable'   => true,
			'default'    => null,
			'allow_null' => true,
		),
		'agent'           => array(
			'name'       => 'agent',
			'type'       => 'varchar',
			'length'     => '255',
			'searchable' => true,
			'sortable'   => true,
			'default'    => null,
			'allow_null' => true,
		),
		'request_method'  => array(
			'name'       => 'request_method',
			'type'       => 'varchar',
			'length'     => '10',
			'sortable'   => true,
			'default'    => 'GET',
			'allow_null' => true,
		),
		'request_data'    => array(
			'name'       => 'request_data',
			'type'       => 'mediumtext',
			'default'    => null,
			'allow_null' => true,
			'validate'   => 'maybe_serialize',
		),
		'hits'            => array(
			'name'     => 'hits',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'sortable' => true,
			'default'  => 1,
		),
		'redirect_status' => array(
			'name'       => 'redirect_status',
			'type'       => 'enum',
			'default'    => 'global',
			'allow_null' => true,
		),
		'log_status'      => array(
			'name'       => 'log_status',
			'type'       => 'enum',
			'default'    => 'global',
			'allow_null' => true,
		),
		'email_status'    => array(
			'name'       => 'email_status',
			'type'       => 'enum',
			'default'    => 'global',
			'allow_null' => true,
		),
		'redirect_id'     => array(
			'name'       => 'redirect_id',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'default'    => null,
			'allow_null' => true,
		),
		'created_at'      => array(
			'name'       => 'created_at',
			'type'       => 'datetime',
			'created'    => true,
			'date_query' => true,
			'sortable'   => true,
			'default'    => '', // Current time.
		),
		'updated_at'      => array(
			'name'       => 'updated_at',
			'type'       => 'datetime',
			'modified'   => true,
			'date_query' => true,
			'sortable'   => true,
			'default'    => null,
			'allow_null' => true,
		),
		'updated_by'      => array(
			'name'       => 'updated_by',
			'type'       => 'bigint',
			'length'     => '20',
			'unsigned'   => true,
			'sortable'   => true,
			'default'    => null,
			'allow_null' => true,
		),
	);
}
