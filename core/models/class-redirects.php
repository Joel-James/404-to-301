<?php
/**
 * The redirect model class.
 *
 * This class handles the database queries for redirects.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Model
 * @subpackage Redirects
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Database;

/**
 * Class Redirects.
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Models
 * @extends Model
 */
class Redirects extends Model {

	/**
	 * Get a redirect by ID.
	 *
	 * @param int $redirect_id Redirect ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return object|false Redirect object if successful, false otherwise.
	 */
	public function get( $redirect_id ) {
		$logs = new Database\Queries\Redirect();

		// Return redirect.
		return $logs->get_item( $redirect_id );
	}

	/**
	 * Get a log by path.
	 *
	 * @param string $path Source path.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return object|false Redirect object if successful, false otherwise.
	 */
	public function get_by_source( $path ) {
		$redirects = new Database\Queries\Redirect();

		// Return redirect.
		return $redirects->get_item_by( 'source', $path );
	}

	/**
	 * Get redirects list.
	 *
	 * Return the redirect data from using the ID.
	 *
	 * @param array $args Filter items using fields.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_redirects( array $args = array() ) {
		// Parse args.
		$args = wp_parse_args(
			$args,
			array(
				'number' => 50,
			)
		);

		// Create a query object.
		$logs = new Database\Queries\Redirect();

		// Return redirects.
		return $logs->query( $args );
	}

	/**
	 * Create a new redirect.
	 *
	 * Make sure to validate all fields before adding it.
	 *
	 * @param array $data Data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function create( array $data ) {
		// Can not continue if url is empty.
		if ( empty( $data['source'] ) ) {
			return false;
		}

		// Create a query object.
		$logs = new Database\Queries\Redirect();

		// Create redirect.
		return $logs->add_item( $data );
	}

	/**
	 * Update an existing redirect.
	 *
	 * @param int   $redirect_id Redirect ID.
	 * @param array $data        Data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function update( $redirect_id, array $data ) {
		// Can not continue if id is empty.
		if ( empty( $redirect_id ) ) {
			return false;
		}

		// Create a query object.
		$logs = new Database\Queries\Redirect();

		// Update redirect.
		return $logs->update_item( $redirect_id, $data );
	}

	/**
	 * Delete a redirect.
	 *
	 * Deleting a redirect won't delete it's error log (if any).
	 *
	 * @param int $redirect_id Redirect ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function delete( $redirect_id ) {
		// Can not continue if id is empty.
		if ( empty( $redirect_id ) ) {
			return false;
		}

		// Create a query object.
		$logs = new Database\Queries\Redirect();

		// Delete redirect.
		return $logs->delete_item( $redirect_id );
	}
}
