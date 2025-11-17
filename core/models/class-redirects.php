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

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\FourNotFour\Database;

/**
 * Class Redirects.
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Models
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
		'type',
		'group',
		'status',
		'meta',
	);

	/**
	 * Query class for the model.
	 *
	 * @since 4.0.0
	 * @var string $query
	 */
	protected $query = '\\DuckDev\\FourNotFour\\Database\Queries\Redirect';

	/**
	 * Initialize class and register hooks.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function __construct() {
		parent::__construct();

		// Make sure to init logs class.
		Logs::instance();

		// Modify redirect data.
		add_filter( '404_to_301_model_redirect_create_data', array( $this, 'filter_log_data' ) );
		add_filter( '404_to_301_model_redirect_update_data', array( $this, 'filter_log_data' ) );
	}

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
		return $this->query()->get_item( $redirect_id );
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
		return $this->query()->get_item_by( 'source', $path );
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

		// Return redirects.
		return $this->query()->query( $args );
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
	 * @return int|false
	 */
	public function create( array $data ) {
		// Can not continue if url is empty.
		if ( empty( $data['source'] ) ) {
			return false;
		}

		/**
		 * Filter to modify final data before redirect creation.
		 *
		 * @since 4.0.0
		 *
		 * @param array $data Data for redirect creation.
		 */
		$data = apply_filters( '404_to_301_model_redirect_create_data', $data );

		// Create new redirect.
		$redirect_id = $this->query()->add_item( $data );

		if ( ! empty( $redirect_id ) ) {
			// Get the created object.
			$redirect = $this->get( $redirect_id );

			/**
			 * Action hook fired after a new redirect is created.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $redirect_id Redirect ID.
			 * @param object $redirect    Redirect object.
			 * @param array  $data        Data used for creation.
			 */
			do_action( '404_to_301_model_after_redirect_create', $redirect_id, $redirect, $data );

			return $redirect_id;
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

		/**
		 * Filter to modify final data before redirect update.
		 *
		 * @since 4.0.0
		 *
		 * @param array $data Data for redirect update.
		 */
		$data = apply_filters( '404_to_301_model_redirect_update_data', $data );

		// Prepare data.
		$data = $this->prepare_fields( $data );

		// Update log.
		if ( ! empty( $data ) && $this->query()->update_item( $redirect_id, $data ) ) {
			// Get the updated object.
			$redirect = $this->get( $redirect_id );

			/**
			 * Action hook fired after a redirect is updated.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $redirect_id Redirect ID.
			 * @param object $redirect    Redirect object.
			 * @param array  $data        Data used for update.
			 */
			do_action( '404_to_301_model_after_redirect_update', $redirect_id, $redirect, $data );

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

		// Get redirect data for action hook.
		$item = $this->get( $redirect_id );

		// Delete log.
		if ( $item && $this->query()->delete_item( $redirect_id ) ) {
			/**
			 * Action hook fired after a redirect is deleted.
			 *
			 * @since 4.0.0
			 *
			 * @param int    $redirect_id Redirect ID.
			 * @param object $item        Redirect object.
			 */
			do_action( '404_to_301_model_after_redirect_delete', $redirect_id, $item );

			return true;
		}

		return false;
	}

	/**
	 * Modify log data before creation.
	 *
	 * Make sure to set a unique hash from URL value.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data Data for log creation.
	 *
	 * @return array
	 */
	public function filter_log_data( array $data ) {
		if ( ! empty( $data['source'] ) ) {
			$data['hash'] = md5( $data['source'] );
		}

		return $data;
	}
}
