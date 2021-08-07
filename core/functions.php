<?php
/**
 * The plugin helper functions.
 *
 * This file contains global functions, mainly used as alias for most used
 * methods from classes.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
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
 * @return DuckDev\Redirect\Settings
 */
function dd4t3_settings() {
	return DuckDev\Redirect\Settings::instance();
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
 * @return DuckDev\Redirect\Cache
 */
function dd4t3_cache() {
	return DuckDev\Redirect\Cache::instance();
}
