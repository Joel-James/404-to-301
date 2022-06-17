<?php
/**
 * The redirect query class.
 *
 * This class will help to make database queries.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Database\Queries
 * @subpackage Redirect
 */

namespace DuckDev\Redirect\Database\Queries;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Redirect.
 *
 * @since   4.0.0
 * @extends Query
 * @package DuckDev\Redirect\Database\Queries
 */
class Redirect extends Query {

	/**
	 * Name of the database table to query.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var   string
	 */
	protected $table_name = 'redirects';

	/**
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access protected
	 */
	protected $prefix = '404_to_301';

	/**
	 * String used to alias the database table in MySQL statement.
	 *
	 * This is used to avoid collisions with JOINs.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var    string
	 */
	protected $table_alias = 'redirects';

	/**
	 * Name of class used to setup the database schema.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var    string
	 */
	protected $table_schema = '\\DuckDev\\Redirect\\Database\\Schemas\\Redirects';

	/**
	 * Name for a single item.
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var    string
	 */
	protected $item_name = 'redirect';

	/**
	 * Plural version for a group of items.
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var    string
	 */
	protected $item_name_plural = 'redirects';

	/**
	 * Name of class used to turn IDs into first-class objects.
	 *
	 * This is used when looping through return values to guarantee their shape.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var    mixed
	 */
	protected $item_shape = '\\DuckDev\\Redirect\\Database\\Rows\\Redirect';
}
