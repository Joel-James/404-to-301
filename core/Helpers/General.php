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
	private static $settings_pages = array(
		'error-logs_page_404-to-301-settings',
	);

	/**
	 * Page screen ids of our plugin pages.
	 *
	 * @var array $plugin_pages
	 */
	private static $logs_pages = array(
		'toplevel_page_404-to-301',
	);

	/**
	 * Check if current page is one of our plugin page.
	 *
	 * @param string $page Page to check.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function is_plugin_page( $page = 'all' ) {
		$current_screen = get_current_screen();

		switch ( $page ) {
			case 'logs':
				$pages = self::$logs_pages;
				break;
			case 'settings':
				$pages = self::$settings_pages;
				break;
			case 'all':
				$pages = array_merge( self::$logs_pages, self::$settings_pages );
				break;
			default:
				$pages = [];
				break;
		}

		// If not on plugin page.
		if ( ! empty( $current_screen->id ) && in_array( $current_screen->id, $pages, true ) ) {
			$is_page = true;
		} else {
			$is_page = false;
		}

		/**
		 * Filter to modify plugin pages check.
		 *
		 * @param bool $is_page Is checking success.
		 */
		return apply_filters( '404_to_301_is_plugin_page', $is_page );
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
