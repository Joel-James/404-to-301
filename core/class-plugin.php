<?php
/**
 * The plugin base class.
 *
 * This class contains the properties of the plugin and the basic
 * information you need about the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @subpackage Core
 */

namespace DuckDev\Redirect;

use DuckDev\Redirect\Abstracts\Base;

/**
 * Creates a new object template.
 *
 * @since  5.0.0
 * @access public
 */
class Plugin extends Base {

	/**
	 * Holds the name of the plugin in class format.
	 *
	 * To access this property, you should use the name().
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var    string
	 */
	private $name = '404-to-301';

	/**
	 * Getter method to get the plugin name.
	 *
	 * @since  5.0.0
	 * @access public
	 * @return bool
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Check if the plugin is active networkwide.
	 *
	 * This is applicable only on a multisite. In single
	 * sites, it will always return false.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_network_wide() {
		return true;
	}
}
