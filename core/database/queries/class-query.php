<?php
/**
 * The base query class.
 *
 * This class extends query class to add few extra things.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
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
	 * @since 4.0.0
	 *
	 * @param string|array $query Array or URL query string of parameters.
	 *
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
	 * @since 4.0.0
	 *
	 * @param array $query Query arguments.
	 *
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
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int $page  Current page no.
	 * @param int $limit No. of items per page.
	 *
	 * @return int
	 */
	private function get_offset( $page = 1, $limit = 100 ) {
		return ( $page - 1 ) * $limit;
	}

	/**
	 * Return the literal table name (with prefix) from the database interface.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_table_name() {
		return $this->get_db()->{$this->table_name};
	}

	/**
	 * Update multiple items using where conditions.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data  Data to update.
	 * @param array $where Where conditions.
	 *
	 * @return bool
	 */
	public function update_multiple( array $data, array $where ) {
		// Can not continue if empty.
		if ( empty( $where ) || empty( $data ) ) {
			return false;
		}

		return $this->get_db()->update(
			$this->get_table_name(),
			$data,
			$where
		);
	}
}
