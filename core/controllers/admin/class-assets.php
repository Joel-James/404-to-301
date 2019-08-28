<?php

namespace DuckDev\WP404\Controllers\Admin;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The admin assets specific functionality of the plugin
 *
 * @link       https://duckdev.com
 * @since      4.0.0
 * @package    Assets
 * @subpackage Admin
 *
 * @author     Joel James <me@joelsays.com>
 */
class Assets extends Base {

	/**
	 * Initilize the class by registering the hooks.
	 *
	 * @since 4.0.0
	 */
	public function init() {
		// Asset hooks.
		add_action( 'admin_enqueue_scripts', [ $this, 'register_settings' ] );
	}

	/**
	 * Register the assets for the admin settings area.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function register_settings() {
		// Register scripts.
		$this->settings_scripts();

		// Register styles.
		$this->settings_styles();

		/**
		 * Action hook to enqueue settings assets.
		 *
		 * Use this action to enqueue styles and scripts
		 * that needs to be loaded in admin pages.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_assets_register_admin_settings' );
	}

	/**
	 * Admin settings page specific scripts.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function settings_scripts() {
		// Settings page script.
		wp_register_script(
			'dd404-settings',
			DD404_URL . 'app/assets/js/settings.js',
			[],
			DD404_VERSION,
			true
		);

		// Localizing items.
		wp_localize_script( 'dd404-settings', 'dd404_settings', array(
				'api_nonce' => wp_create_nonce( 'wp_rest' ),
				'api_url'   => rest_url( '404-to-301/v1/' ),
			)
		);
	}

	/**
	 * Admin settings page specific styles.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function settings_styles() {
		// Settings page style.
		wp_register_style(
			'dd404-settings',
			DD404_URL . 'app/assets/css/settings.min.css',
			[],
			DD404_VERSION,
			'all'
		);
	}
}
