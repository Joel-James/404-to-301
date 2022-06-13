<?php
/**
 * The redirect model class.
 *
 * This class handles the database queries for redirects.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
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
	 * Fields that can be updated.
	 *
	 * @since 4.0.0
	 * @var string[] $updatable
	 */
	protected $updatable = array(
		'source',
		'destination',
		'redirect_status',
		'type',
		'status',
		'meta',
	);

	/**
	 * Get a redirect by ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int $redirect_id Redirect ID.
	 *
	 * @return object|false Redirect object if successful, false otherwise.
	 */
	public function get( $redirect_id ) {
		$redirect = new Database\Queries\Redirect();

		// Return redirect.
		return $redirect->get_item( $redirect_id );
	}

	/**
	 * Get a log by path.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $path Source path.
	 *
	 * @return object|false Redirect object if successful, false otherwise.
	 */
	public function get_by_source( $path ) {
		$redirect = new Database\Queries\Redirect();

		// Return redirect.
		return $redirect->get_item_by( 'source', $path );
	}

	/**
	 * Get redirects list.
	 *
	 * Return the redirect data from using the ID.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $args Filter items using fields.
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
		$redirect = new Database\Queries\Redirect();

		// Return redirects.
		return $redirect->query( $args );
	}

	/**
	 * Create a new redirect.
	 *
	 * Make sure to validate all fields before adding it.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data Data.
	 *
	 * @return bool
	 */
	public function create( array $data ) {
		// Can not continue if url is empty.
		if ( empty( $data['source'] ) ) {
			return false;
		}

		// Create a query object.
		$redirect = new Database\Queries\Redirect();

		// Create redirect.
		if ( $redirect->add_item( $data ) ) {
			/**
			 * Action hook fired after a new redirect is created.
			 *
			 * @since 4.0.0
			 *
			 * @param array $data Data used for redirect.
			 */
			do_action( 'dd4t3_model_after_redirect_create', $data );

			return true;
		}

		return false;
	}

	/**
	 * Update an existing redirect.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int   $redirect_id Redirect ID.
	 * @param array $data        Data.
	 *
	 * @return bool
	 */
	public function update( $redirect_id, array $data ) {
		// Can not continue if id is empty.
		if ( empty( $redirect_id ) ) {
			return false;
		}

		// Create a query object.
		$redirect = new Database\Queries\Redirect();

		// Prepare data.
		$data = $this->prepare_fields( $data );

		// Update log.
		if ( ! empty( $data ) && $redirect->update_item( $redirect_id, $data ) ) {
			/**
			 * Action hook fired after a redirect is updated.
			 *
			 * @since 4.0.0
			 *
			 * @param int   $redirect_id Redirect ID.
			 * @param array $data        Data used for redirect.
			 */
			do_action( 'dd4t3_model_after_redirect_update', $redirect_id, $data );

			return true;
		}

		return false;
	}

	/**
	 * Delete a redirect.
	 *
	 * Deleting a redirect won't delete it's error log (if any).
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param int $redirect_id Redirect ID.
	 *
	 * @return bool
	 */
	public function delete( $redirect_id ) {
		// Can not continue if id is empty.
		if ( empty( $redirect_id ) ) {
			return false;
		}

		// Create a query object.
		$redirect = new Database\Queries\Redirect();

		// Get redirect data for action hook.
		$redirect_data = $this->get( $redirect_id );

		// Delete log.
		if ( $redirect_data && $redirect->delete_item( $redirect_id ) ) {
			/**
			 * Action hook fired after a redirect is deleted.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $redirect_id   Redirect ID.
			 * @param object $redirect_data Redirect data.
			 */
			do_action( 'dd4t3_model_after_redirect_delete', $redirect_id, $redirect_data );

			return true;
		}

		return false;
	}
}
