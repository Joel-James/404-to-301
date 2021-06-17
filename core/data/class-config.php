<?php
/**
 * The plugin permissions class.
 *
 * This class contains the functionality to manage the permissions
 * inside the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Permission
 */

namespace DuckDev\Redirect\Data;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Permission
 *
 * @package DuckDev\Redirect\Controllers
 */
class Config {

	/**
	 * Manage settings capability.
	 *
	 * @since 4.0
	 */
	const SETTINGS_CAP = 'manage_options';
}
