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
 * Class Page
 *
 * @package DuckDev\Redirect\Data
 */
class Page {

	/**
	 * Holds the screen IDs of plugin pages.
	 *
	 * @var string[]
	 * @since  4.0.0
	 * @access private
	 */
	const PAGES = array(
		'logs'      => 'toplevel_page_404-to-301-logs',
		'settings'  => 'logs_page_404-to-301-settings',
		'redirects' => 'logs_page_404-to-301-redirects',
	);

	/**
	 * Get a plugin admin page details.
	 *
	 * @param string $key Key.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string[]|false
	 */
	public static function page( $key = 'settings' ) {
		// Get available pages.
		$pages = self::PAGES;

		// Check if it's available.
		if ( isset( $pages[ $key ] ) ) {
			return array(
				'id'  => $pages[ $key ],
				'url' => admin_url( 'admin.php?page=404-to-301-' . $key ),
			);
		}

		return false;
	}

	/**
	 * Get a plugin admin page URL.
	 *
	 * @param string $key Key.
	 * @param string $tab Tab (Applicable for settings only).
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function url( $key = 'settings', $tab = '' ) {
		$page = self::page( $key );

		$url = $page ? $page['url'] : '';

		// Add tab if required.
		if ( 'settings' === $key && ! empty( $url ) && ! empty( $tab ) ) {
			$url = add_query_arg( 'tab', $tab, $url );
		}

		return $url;
	}
}
