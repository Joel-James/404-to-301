<?php
/**
 * The error logs model class.
 *
 * This class handles the database queries for error logs
 * management.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @since      4.0.0
 * @subpackage Settings
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Model;

/**
 * Class Query
 *
 * @package DuckDev\Redirect\Models
 */
class Query extends Model {

	/**
	 * Retrieves one variable from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row,
	 * the value in the column and row specified is returned. If $query is null,
	 * the value in the specified column and row from the previous SQL result is returned.
	 *
	 * @since 0.71
	 *
	 * @param string|null $query Optional. SQL query. Defaults to null, use the result from the previous query.
	 * @param int         $x     Optional. Column of value to return. Indexed from 0.
	 * @param int         $y     Optional. Row of value to return. Indexed from 0.
	 * @return string|null Database query result (as string), or null on failure.
	 */
	public function get_var() {
		$query = '';

		$results = array();

		$wpdb->get_var();

		/**
		 * Filter to modify the query final results.
		 *
		 * @param array $results Results.
		 * @param Query $this    Query class.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_model_query_results', $results, $this );
	}
}
