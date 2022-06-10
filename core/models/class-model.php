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

/**
 * Class Model
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Models\Model
 */
abstract class Model extends Base {

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
