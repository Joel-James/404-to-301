<?php

namespace DuckDev404\Core\Controllers\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Core\Utils\Abstracts\Base;

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
	 * Register the assets for the admin area.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function register_admin() {
		// Enqueue scripts.
		$this->admin_scripts();

		// Enqueue styles.
		$this->admin_styles();

		/**
		 * Action hook to enqueue settings assets.
		 *
		 * Use this action to enqueue styles and scripts
		 * that needs to be loaded in admin pages.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_assets_register_admin' );
	}

	/**
	 * Admin pages specific scripts.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function admin_scripts() {
		// Settings page script.
		wp_enqueue_script(
			DD404_NAME . '-settings',
			DD404_URL . 'assets/js/admin/admin.min.js',
			array( 'jquery' ),
			DD404_VERSION,
			false
		);
	}

	/**
	 * Admin pages specific styles.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function admin_styles() {
		// Settings page style.
		wp_enqueue_style(
			DD404_NAME . '-settings',
			DD404_URL . 'assets/css/admin.min.css',
			array(),
			DD404_VERSION,
			'all'
		);
	}
}
