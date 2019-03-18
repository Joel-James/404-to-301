<?php

namespace DuckDev404\Inc\Helpers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Define the menu utility functionality.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Menu {

	/**
	 * Plugin admin page tabs.
	 *
	 * @var array Tab names.
	 */
	private static $tabs = array(
		'settings' => array(
			'default',
		),
		'logs'     => array(
			'default',
		),
	);

	/**
	 * Get the current tab being displayed.
	 *
	 * @param string $parent Parent item.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function current_tab( $parent = 'settings' ) {
		$tab = 'default';

		/**
		 * Filter to add items to tabs to current page.
		 *
		 * @since 4.0
		 */
		$tabs = apply_filters( 'dd404_menu_tabs', self::$tabs );

		// Make sure parent item is valid.
		$parent = in_array( $parent, array_keys( $tabs ) ) ? $parent : 'settings';

		// Only if our page.
		if ( General::is_dd404_page() ) {
			// Get tab from url.
			$request_tab = Request::get( 'tab' );

			// Get current tab.
			if ( ! empty( $request_tab ) && in_array( $request_tab, $tabs[ $parent ] ) ) {
				$tab = $request_tab;
			}
		}

		return $tab;
	}

	/**
	 * Get the current menu item.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function current_menu() {
		$menu = 'default';

		// Only if our page.
		if ( General::is_dd404_page() ) {
			$request_menu = Request::get( 'page' );

			// Get current menu.
			if ( ! empty( $request_menu ) && in_array( $request_menu, array_keys( self::$tabs ) ) ) {
				$menu = $request_menu;
			}
		}

		return $menu;
	}
}
