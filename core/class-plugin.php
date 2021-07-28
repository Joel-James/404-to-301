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

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Base;

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
	 * @var    string
	 * @since  4.0.0
	 * @access private
	 */
	private $slug = '404-to-301';

	/**
	 * Holds the name of the plugin in class format.
	 *
	 * To access this property, you should use the name().
	 *
	 * @var    string
	 * @since  4.0.0
	 * @access private
	 */
	private $name = '404 to 301';

	/**
	 * Holds the screen IDs of plugin pages.
	 *
	 * @var string[]
	 * @since  4.0.0
	 * @access private
	 */
	private $pages = array(
		'logs'      => 'toplevel_page_404-to-301-logs',
		'settings'  => 'logs_page_404-to-301-settings',
		'redirects' => 'logs_page_404-to-301-redirects',
	);

	/**
	 * Getter method to get the plugin name.
	 *
	 * @since  5.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function slug() {
		return $this->slug;
	}

	/**
	 * Getter method to get the plugin name.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
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

	/**
	 * Getter method to get the plugin admin page IDs.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string[]
	 */
	public function pages() {
		$pages = array();

		foreach ( $this->pages as $key => $id ) {
			$pages[ $key ] = array(
				'id'  => $id,
				'url' => admin_url( 'admin.php?page=404-to-301-' . $key ),
			);
		}

		return $pages;
	}
}
