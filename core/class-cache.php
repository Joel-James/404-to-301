<?php
/**
 * The plugin cache class.
 *
 * This class contains the functionality to manage the object cache
 * and transients for our plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Cache
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class Cache
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect
 */
class Cache extends Base {

	/**
	 * Group name for object cache.
	 *
	 * @var string
	 * @since 4.0.0
	 */
	const GROUP = '404_to_301';

	/**
	 * Prefix for transients.
	 *
	 * @var string
	 * @since 4.0.0
	 */
	const PREFIX = '404_to_301_';

	/**
	 * Get single item from object cache.
	 *
	 * Wrapper for wp_cache_get function for our plugin.
	 * Use this to get the cache values set using set_cache method.
	 *
	 * @param int|string $key       The key under which the cache contents are stored.
	 * @param string     $group     Optional. Where the cache contents are grouped.
	 * @param bool       $force     Optional. Whether to force an update of the local
	 *                              cache from the persistent cache. Default false.
	 * @param bool       $found     Optional. Whether the key was found in the cache (passed by reference).
	 *                              Disambiguate a return of false, a storable value. Default null.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool|mixed False on failure to retrieve contents or the cache contents on success
	 */
	public function get( $key, $group = self::GROUP, $force = false, &$found = null ) {
		// Check if caching disabled.
		if ( ! $this->can_cache( $key, $group ) ) {
			return false;
		}

		return wp_cache_get( $key, $group, $force, $found );
	}

	/**
	 * Get single item from transients.
	 *
	 * Wrapper for get_site_transient function with our own prefix.
	 *
	 * @param string $key          Transient name. Expected to not be SQL-escaped.
	 * @param bool   $skip_filters Should skip transient enable/disable filters.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return mixed Value of transient.
	 */
	public function get_transient( $key, $skip_filters = false ) {
		// Check if transients disabled.
		if ( ! $skip_filters && ! $this->can_transient( $key ) ) {
			return false;
		}

		return get_site_transient( $this->key( $key ) );
	}

	/**
	 * Set a single item to object cache.
	 *
	 * Wrapper for wp_cache_set in Beehive.
	 *
	 * @param int|string $key       The cache key to use for retrieval later.
	 * @param mixed      $data      The contents to store in the cache.
	 * @param string     $group     Optional. Where to group the cache contents.
	 *                              Enables the same key to be used across groups.
	 * @param int        $expire    Optional. When to expire the cache contents, in seconds.
	 *                              Default 0 (no expiration).
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool False on failure, true on success.
	 */
	public function set( $key, $data, $group = self::GROUP, $expire = 0 ) {
		// Check if caching disabled.
		if ( ! $this->can_cache( $key, $group ) ) {
			return false;
		}

		// Set to WP cache.
		return wp_cache_set( $key, $data, $group, $expire );
	}

	/**
	 * Set a single item to transient.
	 *
	 * Wrapper for set_site_transient with our own prefix.
	 *
	 * @param string $key          Transient name. Expected to not be SQL-escaped. Must be
	 *                             167 characters or fewer in length.
	 * @param mixed  $data         Transient value. Expected to not be SQL-escaped.
	 * @param bool   $skip_filters Should skip transient enable/disable filters.
	 * @param int    $expire       Optional. Time until expiration in seconds. Default 0 (no expiration).
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool True if the value was set, false otherwise.
	 */
	public function set_transient( $key, $data, $skip_filters = false, $expire = 0 ) {
		// Check if transients disabled.
		if ( ! $skip_filters && ! $this->can_transient( $key ) ) {
			return false;
		}

		// Set to WP transients.
		return set_site_transient( $this->key( $key ), $data, $expire );
	}

	/**
	 * Delete a single item from the cache.
	 *
	 * This is a wrapper function for wp_cache_delete.
	 *
	 * @param int|string $key   The key under which the cache contents are stored.
	 * @param string     $group Optional. Where the cache contents are grouped.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function delete( $key, $group = self::GROUP ) {
		// Delete object cache.
		return wp_cache_delete( $key, $group );
	}

	/**
	 * Delete a single item from the transient.
	 *
	 * This is a wrapper function for delete_site_transient with our prefix.
	 *
	 * @param int|string $key Transient name. Expected to not be SQL-escaped.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function delete_transient( $key ) {
		// Delete transient.
		return delete_site_transient( $this->key( $key ) );
	}

	/**
	 * Prefix the cache key with our own prefix.
	 *
	 * @param string $key Key.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool $enable_cache
	 */
	private function key( $key ) {
		$key = self::PREFIX . $key;

		/**
		 * Prefix base for cache keys.
		 *
		 * Only used for transients at the moment.
		 *
		 * @param string $key Key of item.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_cache_key', $key );
	}

	/**
	 * Check if we use cache for our plugin.
	 *
	 * Object caching can be disabled by returning false to
	 * dd404_can_cache filter.
	 *
	 * @param string $key   The key under which the cache contents are stored.
	 * @param string $group Where the cache contents are grouped.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool $enable_cache
	 */
	private function can_cache( $key, $group ) {
		/**
		 * Make caching optional.
		 *
		 * Use filter to toggle cache enable status.
		 *
		 * @param bool   $can_cache Should cache?.
		 * @param string $key       Key of cache item.
		 * @param string $group     Group name of cache.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_can_cache', true, $key, $group );
	}

	/**
	 * Check if we use transient for our plugin.
	 *
	 * Transient caching can be disabled by returning false to
	 * dd404_can_cache filter.
	 *
	 * @param string $key Transient key.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool
	 */
	private function can_transient( $key ) {
		/**
		 * Make transient usage optional.
		 *
		 * Use filter to toggle transient enable status.
		 *
		 * @param bool   $can_transient Should cache?.
		 * @param string $key           Key of cache item.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_can_transient', true, $key );
	}
}
