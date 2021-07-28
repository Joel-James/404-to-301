<?php
/**
 * The plugin settings helper functions.
 *
 * This file contains settings related global functions.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Functions
 * @subpackage Settings
 */

namespace {

	/**
	 * Get the plugin settings instance.
	 *
	 * Use this to access the plugin settings class from
	 * anywhere in the WP.
	 *
	 * @since 4.0.0
	 *
	 * @return DuckDev\Redirect\Settings
	 */
	function dd404_settings() {
		return DuckDev\Redirect\Settings::instance();
	}
}
