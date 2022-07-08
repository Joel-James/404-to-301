<?php
/**
 * Singleton class for all models.
 *
 * Extend this class whenever possible to make use of common
 * methods.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Model
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Base;
use DuckDev\Redirect\Database\Queries;

/**
 * Class Model
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Models\Model
 */
abstract class Model extends Base {

	/**
	 * Fields that can be updated.
	 *
	 * @since 4.0.0
	 * @var string[] $updatable
	 */
	protected $updatable = array();

	/**
	 * Query class for the model.
	 *
	 * @since 4.0.0
	 * @var string $query
	 */
	protected $query;

	/**
	 * Get a new instance of query class.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return Queries\Query
	 */
	protected function query() {
		return new $this->query();
	}

	/**
	 * Get the no. of items found on the table.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return int
	 */
	public function count() {
		return $this->query()->query( array( 'count' => true ) );
	}

	/**
	 * Check if at least one log is found.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function has_items() {
		return $this->count() > 0;
	}

	/**
	 * Use object cache for model data.
	 *
	 * Get from cache before making complex db cals.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @param string   $key      Cache key.
	 * @param callable $callback Callback.
	 *
	 * @return false|mixed
	 */
	protected function remember( $key, $callback ) {
		// Use cache.
		$log = dd4t3_cache()->remember( $key, $callback );

		return empty( $log ) ? false : $log;
	}

	/**
	 * Filter out unwanted fields from update.
	 *
	 * Only items listed in $updatable property will be allowed.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $data Data to update.
	 */
	protected function prepare_fields( $data ) {
		if ( empty( $this->updatable ) || empty( $data ) ) {
			return $data;
		}

		// Loop and remove unwanted items.
		foreach ( $data as $field => $value ) {
			if ( ! in_array( $field, $this->updatable, true ) ) {
				unset( $data[ $field ] );
			}
		}

		/**
		 * Filter hook to modify filtered data for update.
		 *
		 * @since 4.0.0
		 *
		 * @param array $data Filtered data.
		 */
		return apply_filters( 'dd4t3_model_prepare_fields', $data );
	}

	/**
	 * Set query arguments in supported format.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @param array $raw_args Arguments.
	 *
	 * @return array
	 */
	protected function format_args( array $raw_args ) {
		return $raw_args;
	}
}
