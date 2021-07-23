<?php
/**
 * The plugin settings helper functions.
 *
 * This file contains settings related functions.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Functions
 * @subpackage Settings
 */

/**
 * The SEO Framework plugin
 * Copyright (C) 2018 - 2021 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace {

	/**
	 * Get the plugin settings data.
	 *
	 * This will return the full settings.
	 * If there are extra fields which is not registered
	 * into default settings, we won't return it.
	 *
	 * @since 4.0.0
	 *
	 * @return DuckDev\Redirect\Controllers\Settings
	 */
	function dd404_settings() {
		return DuckDev\Redirect\Controllers\Settings::instance();
	}
}
