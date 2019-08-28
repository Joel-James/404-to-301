<?php

namespace DuckDev\WP404\Views\Admin;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Helpers;
use DuckDev\WP404\Utils\Abstracts\Base;


/**
 * The page-specific functionality of the plugin.
 *
 * Loading page specific views are handled in this class.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Pages extends Base {

	/**
	 * View for the error logs page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function logs() {
		// Get args for the page.
		$args = array();

		// Error logs page content.
		Helpers\General::view( 'admin/common/header' );
		Helpers\General::view( 'admin/logs', $args );
		Helpers\General::view( 'admin/common/footer' );

		/**
		 * Action hook to run after printing logs page content.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_logs_page_content' );
	}

	/**
	 * View for the plugin settings page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function settings() {
		Helpers\General::view( 'admin/settings' );

		// Enqueue the scripts.
		wp_enqueue_script( 'dd404-settings' );

		/**
		 * Action hook to run after printing settings page content.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_setting_page_content' );
	}
}