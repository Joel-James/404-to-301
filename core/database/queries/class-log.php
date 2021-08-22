<?php
/**
 * The log query class.
 *
 * This class will help to make database queries.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Queries
 * @subpackage Log
 */

namespace DuckDev\Redirect\Database\Queries;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Query;

/**
 * Class Log.
 *
 * @since   4.0.0
 * @extends Query
 * @package DuckDev\Redirect\Database\Queries
 */
class Log extends Query {

	/**
	 * Name of the database table to query.
	 *
	 * @var   string
	 * @since  4.0.0
	 * @access protected
	 */
	protected $table_name = '404_to_301_logs';

	/**
	 * String used to alias the database table in MySQL statement.
	 *
	 * This is used to avoid collisions with JOINs.
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access protected
	 */
	protected $table_alias = 'logs';

	/**
	 * Name of class used to setup the database schema.
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access protected
	 */
	protected $table_schema = '\\DuckDev\\Redirect\\Database\\Schemas\\Logs';

	/**
	 * Name for a single item.
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access protected
	 */
	protected $item_name = 'log';

	/**
	 * Plural version for a group of items.
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access protected
	 */
	protected $item_name_plural = 'logs';

	/**
	 * Name of class used to turn IDs into first-class objects.
	 *
	 * This is used when looping through return values to guarantee their shape.
	 *
	 * @var    mixed
	 * @since  4.0.0
	 * @access protected
	 */
	protected $item_shape = '\\DuckDev\\Redirect\\Database\\Rows\\Log';
}
