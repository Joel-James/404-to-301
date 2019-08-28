<?php

namespace DuckDev\WP404\Helpers;

// Direct hit? Rest in peace..
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
		'toplevel_page_404-to-301',
		'error-logs_page_404-to-301-settings',
	);

	/**
	 * Check if current page is DD Boilerplate admin.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function is_our_page() {
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
			$file_name = DD404_DIR . 'app/templates/' . $view . '.php';
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
			Logs::error_log( sprintf( __( '%1$s, view missing or not readable: %2$s', '404-to-301' ), '404 to 301', $file_name ) );
		}
	}
}
