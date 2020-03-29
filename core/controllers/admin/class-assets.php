<?php

namespace DuckDev\WP404\Controllers\Admin;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;
use DuckDev\WP404\Controllers\Common\I18n;

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
	 * Initialize assets functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'register' ] );
	}

	/**
	 * Assets for our front end functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function register() {
		$this->register_styles();
		$this->register_scripts();
	}

	/**
	 * Register available styles.
	 *
	 * We are just registering the assets with WP now.
	 * We will enqueue them when it's really required.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	private function register_styles() {
		// Get all the assets.
		$styles = $this->get_styles();

		// Register all styles.
		foreach ( $styles as $handle => $data ) {
			// Register custom videos scripts.
			wp_register_style(
				$handle,
				DD404_URL . '/app/assets/css/' . $data['src'],
				empty( $data['deps'] ) ? [] : $data['deps'],
				empty( $data['version'] ) ? null : $data['version'],
				empty( $data['media'] ) ? false : true
			);
		}
	}

	/**
	 * Register available scripts.
	 *
	 * We are just registering the assets with WP now.
	 * We will enqueue them when it's really required.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	private function register_scripts() {
		// Get all the assets.
		$scripts = $this->get_scripts();

		// Register all available scripts.
		foreach ( $scripts as $handle => $data ) {
			// Register custom videos scripts.
			wp_register_script(
				$handle,
				DD404_URL . '/app/assets/js/' . $data['src'],
				empty( $data['deps'] ) ? [] : $data['deps'],
				empty( $data['version'] ) ? null : $data['version'],
				isset( $data['footer'] ) ? $data['footer'] : true
			);
		}
	}

	/**
	 * Enqueue a style with WordPress.
	 *
	 * This is just an alias function.
	 *
	 * @param string $style Style handle name.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function enqueue_style( $style ) {
		// Only if not enqueued already.
		if ( ! wp_style_is( $style ) ) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * Enqueue a script with localization.
	 *
	 * Always use this method to enqueue scripts. Then only
	 * we will get the required localized vars.
	 *
	 * @param string $script Script handle name.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function enqueue_script( $script ) {
		// Only if not enqueued already.
		if ( ! wp_script_is( $script ) ) {
			// Extra vars.
			wp_localize_script( $script,
				'dd4t3ModuleVars',
				/**
				 * Filter to add/remove vars in script.
				 *
				 * @since 4.0.0
				 */
				apply_filters( "404_to_301_assets_scripts_localize_vars_{$script}", [] )
			);

			// Common vars.
			$common_vars = [];

			wp_localize_script( $script,
				'dd4t3Vars',
				/**
				 * Filter to add/remove vars in script.
				 *
				 * @param array $common_vars Common vars.
				 * @param array $handle      Script handle name.
				 *
				 * @since 4.0.0
				 */
				apply_filters( '404_to_301_script_vars', $common_vars, $script )
			);

			error_log('test');

			// Localized vars for the locale.
			wp_localize_script( $script, 'dd4t3i18n', I18n::_get()->get_strings( $script ) );

			// Now enqueue.
			wp_enqueue_script( $script );
		}
	}

	/**
	 * Get the scripts list to register.
	 *
	 * @since 3.2.4
	 *
	 * @return array
	 */
	private function get_scripts() {
		$scripts = [
			'404-to-301-settings' => [
				'src'  => 'settings.min.js',
				'deps' => [ 'jquery', '404-to-301-vendors', '404-to-301-common' ],
			],
			'404-to-301-logs'     => [
				'src'  => 'logs.min.js',
				'deps' => [ 'jquery', '404-to-301-vendors', '404-to-301-common' ],
			],
			'404-to-301-vendors'  => [
				'src' => 'chunk-vendors.min.js',
			],
			'404-to-301-common'   => [
				'src' => 'chunk-common.min.js',
			],
		];

		/**
		 * Filter to include/exclude new script.
		 *
		 * Modules should use this filter to that common localized
		 * vars will be available.
		 *
		 * @param array $scripts Scripts list.
		 *
		 * @since 3.2.4
		 */
		return apply_filters( '404_to_301_assets_get_scripts', $scripts );
	}

	/**
	 * Get the styles list to register.
	 *
	 * @since 3.2.4
	 *
	 * @return array
	 */
	private function get_styles() {
		$styles = [
			'404-to-301-settings' => [
				'src' => 'settings.min.css',
			],
			'404-to-301-logs'     => [
				'src' => 'logs.min.css',
			],
		];

		/**
		 * Filter to include/exclude new style.
		 *
		 * Modules should use this filter to include styles.
		 *
		 * @param array $styles Styles list.
		 *
		 * @since 3.2.4
		 */
		return apply_filters( '404_to_301_assets_get_styles', $styles );
	}
}