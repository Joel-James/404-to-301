<?php

namespace DuckDev\WP404\Helpers;

// Direct hit? Rest in peace..
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
	 * Plugin admin page tabs list.
	 *
	 * @param string $parent The parent item.
	 *
	 * @since 4.0
	 *
	 * @return array Tab names.
	 */
	public static function tabs( $parent = 'settings' ) {
		$tabs = [
			'settings' => [
				'general' => [
					'label' => __( 'Settings', '404-to-301' ),
					'icon'  => 'dashicons-admin-generic',
				],
			],
			'logs'     => [
				'settings' => [
					'label' => __( 'Settings', '404-to-301' ),
					'icon'  => 'dashicons-admin-generic',
				],
			],
		];

		/**
		 * Filter to add/remove menu items in admin pages.
		 *
		 * @param array $menu_items Menu items.
		 *
		 * @since 4.0
		 */
		$tabs = apply_filters( 'dd404_admin_menu_tabs', $tabs );

		// Incase parent is not mentioned.
		if ( empty( $parent ) ) {
			return $tabs;
		}

		return isset( $tabs[ $parent ] ) ? $tabs[ $parent ] : [];
	}

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
		$tab  = 'general';
		$tabs = self::tabs();

		// Make sure parent item is valid.
		$parent = in_array( $parent, array_keys( $tabs ) ) ? $parent : 'settings';

		// Only if our page.
		if ( General::is_our_page() ) {
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
		$menu = 'settings';

		// Only if our page.
		if ( General::is_our_page() ) {
			$request_menu = Request::get( 'page' );

			// Get current menu.
			if ( ! empty( $request_menu ) && in_array( $request_menu, array_keys( self::tabs() ) ) ) {
				$menu = $request_menu;
			}
		}

		return $menu;
	}

	/**
	 * Render admin menu for Pro Sites dashboard.
	 *
	 * @param string $current_item Current active item.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public static function render_settings_menu( $current_item ) {
		General::view( 'admin/common/menu', array(
			'tabs' => self::tabs( 'settings' ),
			'tab'  => $current_item,
		) );
	}
}
