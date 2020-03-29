<?php

namespace DuckDev\WP404\Views;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Helpers;
use DuckDev\WP404\Utils\Abstracts\Base;
use DuckDev\WP404\Controllers\Admin\Assets;


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
		// Enqueue the assets.
		Assets::_get()->enqueue_style( '404-to-301-logs' );
		Assets::_get()->enqueue_script( '404-to-301-logs' );

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
		// Enqueue the assets.
		Assets::_get()->enqueue_style( '404-to-301-settings' );
		Assets::_get()->enqueue_script( '404-to-301-settings' );

		// Settings page content.
		Helpers\General::view( 'admin/common/header' );
		Helpers\General::view( 'admin/settings' );
		Helpers\General::view( 'admin/common/footer' );

		/**
		 * Action hook to run after printing settings page content.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_setting_page_content' );
	}
}