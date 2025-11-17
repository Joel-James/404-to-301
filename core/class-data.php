<?php
/**
 * The plugin data class.
 *
 * This class contains the data objects for the plugin.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Data
 */

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Data
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
class Data {

	/**
	 * Get available redirect types.
	 *
	 * To add or remove redirect types, use 404_to_301_redirect_types filter.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return mixed|void
	 */
	public static function redirect_types() {
		$types = array(
			301 => __( '301 - Moved Permanently', '404-to-301' ),
			302 => __( '302 - Found', '404-to-301' ),
			303 => __( '303 - See Other', '404-to-301' ),
			304 => __( '304 - Not Modified', '404-to-301' ),
			307 => __( '307 - Temporary Redirect', '404-to-301' ),
			308 => __( '308 - Permanent Redirect', '404-to-301' ),
		);

		/**
		 * Filter to add or remove redirect types.
		 *
		 * @since 4.0.0
		 *
		 * @param array $types Redirect types.
		 */
		return apply_filters( '404_to_301_redirect_types', $types );
	}

	/**
	 * Get available addons list.
	 *
	 * To add or remove an addon, use 404_to_301_addons_list filter.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function addons() {
		static $addons = array();

		if ( empty( $addons ) ) {
			$handler = new Addon();

			// Add additional info.
			foreach ( $handler->get_addons() as $id => $addon ) {
				$addon['slug']         = $id;
				$addon['is_active']    = $handler->is_active( $id );
				$addon['is_installed'] = $handler->is_installed( $id );
				$addons[]              = $addon;
			}
		}

		/**
		 * Filter to add or remove addons.
		 *
		 * @since 4.0.0
		 *
		 * @param array $addons Addon list.
		 */
		return apply_filters( '404_to_301_addons', $addons );
	}

	/**
	 * Get the list of pages.
	 *
	 * Only 100 pages will be returned.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function pages() {
		static $pages = array();

		if ( empty( $pages ) ) {
			$args = array( 'number' => 100 );

			/**
			 * Filter to modify arguments for get_pages query.
			 *
			 * @since 4.0.0
			 *
			 * @param array $args Arguments.
			 */
			$args = apply_filters( '404_to_301_wp_pages_args', $args );

			// Get WP pages.
			$wp_pages = get_pages( $args );

			// Get only titles.
			$pages = wp_list_pluck( $wp_pages, 'post_title', 'ID' );
		}

		/**
		 * Filter to add or remove pages.
		 *
		 * @since 4.0.0
		 *
		 * @param array $pages Page list.
		 */
		return apply_filters( '404_to_301_wp_pages', $pages );
	}
}
