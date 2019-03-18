<?php

namespace DuckDev404\Inc\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Helpers\General;
use DuckDev404\Inc\Core\Base;

/**
 * The assets-specific functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Assets extends Base {

	/**
	 * Initialize the class by registering hooks.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function init() {
		// Register admin scripts.
		$this->add_action( 'admin_enqueue_scripts', 'assets' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function assets() {
		// Continue only if current page is one of DD Boilerplate pages.
		if ( ! General::is_dd404_page() ) {
			return;
		}

		// Enqueue settings page assets.
		$this->settings();

		// Enqueue common assets.
		$this->common();
	}

	/**
	 * Settings page specific styles and scripts.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function settings() {
		// Settings page script.
		wp_enqueue_script(
			DD404_NAME . '-settings',
			DD404_URL . 'assets/js/admin/admin.min.js',
			array( 'jquery' ),
			DD404_VERSION,
			false
		);

		// Settings page style.
		wp_enqueue_style(
			DD404_NAME . '-settings',
			DD404_URL . 'assets/css/admin.min.css',
			array(),
			DD404_VERSION,
			'all'
		);

		/**
		 * Action hook to enqueue settings assets.
		 *
		 * Use this action to enqueue styles and scripts
		 * that needs to be loaded in settings page.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_assets_settings' );
	}

	/**
	 * Assets that is required in common.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function common() {
		/**
		 * Action hook to enqueue common assets.
		 *
		 * Use this action to enqueue styles and scripts
		 * that are common in all pro sites admin pages.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_assets_common' );
	}
}
