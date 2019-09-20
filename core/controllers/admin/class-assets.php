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
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'register' ], 5 );
		} else {
			add_action( 'wp_enqueue_scripts', [ $this, 'register' ], 5 );
		}
	}

	/**
	 * Register the scripts and styles.
	 *
	 * Do not enqueue the assets now. We can do that
	 * when required.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register() {
		$this->register_scripts( $this->get_scripts() );
		$this->register_styles( $this->get_styles() );
	}

	/**
	 * Register scripts with WordPress.
	 *
	 * Whenever possible, load the scripts in footer.
	 *
	 * @param array $scripts Scripts list.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function register_scripts( $scripts ) {
		foreach ( $scripts as $handle => $script ) {
			// Prepare the data.
			$deps      = isset( $script['deps'] ) ? $script['deps'] : false;
			$in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
			$version   = isset( $script['version'] ) ? $script['version'] : DD404_VERSION;

			// Now register.
			wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
		}
	}

	/**
	 * Register styles with WordPress.
	 *
	 * Register stylesheets using wp_register_style so that we
	 * can enqueue them when required.
	 *
	 * @param array $styles Styles list.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_styles( $styles ) {
		foreach ( $styles as $handle => $style ) {
			// Prepare the data.
			$deps = isset( $style['deps'] ) ? $style['deps'] : false;

			// Now register the style.
			wp_register_style( $handle, $style['src'], $deps, DD404_VERSION );
		}
	}

	/**
	 * Get all registered scripts array.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function get_scripts() {
		$scripts = [
			'dd404-vendor'   => [
				'src'       => DD404_URL . '/app/assets/js/vendor.js',
				'in_footer' => true,
			],
			'dd404-frontend' => [
				'src'       => DD404_URL . '/app/assets/js/frontend.js',
				'deps'      => [ 'jquery', 'dd404-vendor' ],
				'in_footer' => true,
			],
			'dd404-settings' => [
				'src'       => DD404_URL . '/app/assets/js/settings.js',
				'deps'      => [ 'jquery', 'dd404-vendor' ],
				'in_footer' => true,
			],
			'dd404-logs' => [
				'src'       => DD404_URL . '/app/assets/js/logs.js',
				'deps'      => [ 'jquery', 'dd404-vendor' ],
				'in_footer' => true,
			],
		];

		/**
		 * Filter hook to modify the scripts list.
		 *
		 * @param array $scripts Scripts list.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_scripts_list', $scripts );
	}

	/**
	 * Get registered styles array.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function get_styles() {
		$styles = [
			'dd404-style'    => [
				'src' => DD404_URL . '/app/assets/css/style.css',
			],
			'dd404-frontend' => [
				'src' => DD404_URL . '/app/assets/css/frontend.css',
			],
			'dd404-settings' => [
				'src' => DD404_URL . '/app/assets/css/settings.css',
			],
		];

		/**
		 * Filter hook to modify the styles list.
		 *
		 * @param array $scripts Styles list.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_styles_list', $styles );
	}
}
