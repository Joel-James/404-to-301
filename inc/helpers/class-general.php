<?php

namespace DuckDev404\Inc\Helpers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Define the general utility functionality.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class General {

	/**
	 * Page screen ids of our plugin pages.
	 *
	 * @var array $plugin_pages
	 */
	private static $plugin_pages = array(
		'toplevel_page_dd404-logs',
		'404-errors_page_dd404-settings',
	);

	/**
	 * Check if current page is DD Boilerplate admin.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function is_dd404_page() {
		$current_screen = get_current_screen();

		/**
		 * Filter to add/remove items to DD Boilerplate pages.
		 *
		 * @param array $plugin_pages Screen IDs of 404 to 301 pages.
		 */
		$plugin_pages = apply_filters( 'dd404_plugin_pages', self::$plugin_pages );

		// If not on plugin page.
		if ( empty( $current_screen ) || ! in_array( $current_screen->id, $plugin_pages, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Print the given admin view file.
	 *
	 * @param string $view The relative path of the file.
	 * @param array  $args Optional arguments to set as variable
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return void
	 */
	public static function view( $view, $args = array() ) {
		// Custom file path.
		if ( file_exists( DD404_DIR . $view . '.php' ) ) {
			$file_name = DD404_DIR . $view . '.php';
		} else {
			// Default views.
			$file_name = DD404_DIR . 'views/' . $view . '.php';
		}

		// If file exist, set all arguments are variables.
		if ( file_exists( $file_name ) && is_readable( $file_name ) ) {
			if ( ! empty( $args ) ) {
				$args = (array) $args;
				extract( $args );
			}
			// Now include the file.
			include $file_name;
		} else {
			// Log error.
			Logs::error_log( sprintf( __( '%1$s, view missing or not readable: %2$s', '404-to-301' ), DD404_NAME, $file_name ) );
		}
	}

	/**
	 * Render admin menu for Pro Sites dashboard.
	 *
	 * @param array  $menu_items   Menu items (array of key and title).
	 * @param string $current_item Current active item.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public static function render_menu( $menu_items, $current_item ) {
		self::view( 'admin/common/menu', array(
			'tabs' => $menu_items,
			'tab'  => $current_item,
		) );
	}
}
