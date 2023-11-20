<?php
/**
 * The plugin helper functions.
 *
 * This file contains global functions, mainly used as alias for most used
 * methods from classes.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Functions
 */

/**
 * Get the plugin settings instance.
 *
 * Use this to access the plugin settings class from
 * anywhere in the WP.
 *
 * @since  4.0.0
 * @access public
 *
 * @return RedirectPress\Settings
 */
function redirectpress_settings() {
	return RedirectPress\Settings::instance();
}

/**
 * Get the plugin cache instance.
 *
 * Use this to access the plugin cache class from
 * anywhere in the WP.
 *
 * @since  4.0.0
 * @access public
 *
 * @return RedirectPress\Cache
 */
function redirectpress_cache() {
	static $cache = null;

	// Make sure only one instance is available.
	if ( null === $cache ) {
		$cache = new RedirectPress\Cache();
	}

	return $cache;
}
