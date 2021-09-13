<?php
/**
 * The base query class.
 *
 * This class extends query class to add few extra things.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Queries
 * @subpackage Query
 */

namespace DuckDev\Redirect\Database\Queries;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Query.
 *
 * @since   4.0.0
 * @extends \BerlinDB\Database\Query
 * @package DuckDev\Redirect\Database\Queries
 */
class Query extends \BerlinDB\Database\Query {

	/**
	 * Override parent query method to modify arguments.
	 *
	 * @param string|array $query Array or URL query string of parameters.
	 *
	 * @since 4.0.0
	 * @return array|int List of items, or number of items when 'count' is passed as a query var.
	 */
	public function query( $query = array() ) {
		// Modify arguments if required.
		$query = $this->process_args( $query );

		return parent::query( $query );
	}

	/**
	 * Add support for new arguments and convert them.
	 *
	 * Convert our custom arguments to query supported format.
	 *
	 * @param array $query Query arguments.
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function process_args( $query = array() ) {
		// No need to continue if empty.
		if ( empty( $query ) ) {
			return $query;
		}

		// Add pagination page support.
		if ( ! isset( $query['offset'] ) && isset( $query['page'] ) ) {
			// Get the limit.
			$limit = isset( $query['number'] ) ? $query['number'] : 100;
			// Set offset.
			$query['offset'] = $this->get_offset( $query['page'], $limit );
			// Unset unsupported args.
			unset( $query['page'] );
		}

		return $query;
	}

	/**
	 * Get the offset value for query.
	 *
	 * @param int $page  Current page no.
	 * @param int $limit No. of items per page.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return int
	 */
	private function get_offset( $page = 1, $limit = 100 ) {
		return ( $page - 1 ) * $limit;
	}
}
