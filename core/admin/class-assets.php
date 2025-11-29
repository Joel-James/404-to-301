<?php
/**
 * The plugin assets class.
 *
 * This class contains the functionality to manage the assets
 * inside the plugin admin screens.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    Admin
 * @subpackage Assets
 */

namespace DuckDev\FourNotFour\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\FourNotFour\Plugin;
use DuckDev\FourNotFour\Utils\Base;

/**
 * Class Assets
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\FourNotFour\Admin
 */
class Assets extends Base {

	/**
	 * Initialize assets functionality.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		// Setup assets first.
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		// Enqueue assets only on plugin pages.
		add_action( '404_to_301_enqueue_assets_logs', array( $this, 'logs_assets' ) );
		add_action( '404_to_301_enqueue_assets_settings', array( $this, 'settings_assets' ) );
		add_action( '404_to_301_enqueue_assets_redirects', array( $this, 'redirects_assets' ) );
	}

	/**
	 * Register and set up enqueue hooks for assets.
	 *
	 * Assets will be enqueued only when required on the page.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function assets( string $hook_suffix ) {
		// Register assets.
		$this->register_assets();

		// Setup enqueue actions only for our pages.
		$this->do_enqueue_action( $hook_suffix );
	}

	/**
	 * Enqueue a script along with localization data.
	 *
	 * Always use this method to enqueue scripts. Then only
	 * we will get the required localized vars added by plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 * @uses   wp_script_is()
	 * @uses   wp_localize_script()
	 * @uses   wp_enqueue_script()
	 * @uses   wp_set_script_translations()
	 *
	 * @param string $script Script handle name.
	 *
	 * @return void
	 */
	public function enqueue_script( string $script ) {
		// Only if not enqueued already.
		if ( ! wp_script_is( $script ) ) {
			// Script vars.
			wp_localize_script(
				$script,
				'duckdev404',
				/**
				 * Filter to add/remove vars in script.
				 *
				 * @since 4.0.0
				 *
				 * @param array $vars Localize vars.
				 */
				apply_filters( "404_to_301_assets_vars_{$script}", array() )
			);

			// Enqueue script now.
			wp_enqueue_script( $script );

			// Javascript translations.
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations(
					$script,
					'404-to-301',
					DUCKDEV_404_DIR . '/languages/'
				);
			}
		}
	}

	/**
	 * Enqueue a style with WordPress.
	 *
	 * This is just an alias for wp_enqueue_style().
	 *
	 * @since  4.0.0
	 * @access public
	 * @uses   wp_enqueue_style()
	 * @uses   wp_style_is()
	 *
	 * @param string $style Style handle name.
	 *
	 * @return void
	 */
	public function enqueue_style( string $style ) {
		// Only if not enqueued already.
		if ( ! wp_style_is( $style ) ) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * Enqueue assets for the logs page.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function logs_assets() {
		$this->enqueue_script( '404-to-301-logs' );
		$this->enqueue_style( '404-to-301-logs' );
	}

	/**
	 * Enqueue assets for the settings page.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function settings_assets() {
		$this->enqueue_script( '404-to-301-settings' );
		$this->enqueue_style( '404-to-301-settings' );
	}

	/**
	 * Enqueue assets for the redirects page.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function redirects_assets() {
		$this->enqueue_script( '404-to-301-redirects' );
		$this->enqueue_style( '404-to-301-redirects' );
	}

	/**
	 * Set up action hooks for assets enqueue.
	 *
	 * We will register new action hook only if the current
	 * page is one of plugin's admin page.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	private function do_enqueue_action( string $hook_suffix ) {
		// Check if current page is one of our pages.
		$page = array_search( $hook_suffix, Plugin::screens(), true );

		// If our page.
		if ( ! empty( $page ) ) {
			/**
			 * Action hook to enqueue assets for our pages.
			 *
			 * Use this hook to enqueue scripts and styles which are
			 * only loaded on our plugins pages. This hook will be
			 * fired only on specified page.
			 *
			 * @since 4.0.0
			 *
			 * @param string $page        Current page key.
			 * @param string $hook_suffix The current admin page.
			 */
			do_action( "404_to_301_enqueue_assets_{$page}", $page, $hook_suffix );

			/**
			 * Action hook to enqueue assets for our pages.
			 *
			 * This hook will be fired for all plugin pages.
			 *
			 * @since 4.0.0
			 *
			 * @param string $page        Current page key.
			 * @param string $hook_suffix The current admin page.
			 */
			do_action( '404_to_301_enqueue_assets', $page, $hook_suffix );
		}
	}

	/**
	 * Register all provided assets.
	 *
	 * We are just registering the scripts and styles with WP now.
	 * We will enqueue them when it's really required.
	 * To enqueue a script use Assets::enqueue_script().
	 * To enqueue a script use Assets::enqueue_style().
	 *
	 * @since  4.0.0
	 * @access private
	 * @see    Assets::enqueue_script().
	 * @uses   wp_register_script()
	 *
	 * @return void
	 */
	private function register_assets() {
		foreach ( $this->get_assets() as $handle => $assets ) {
			// Register JS files.
			if ( isset( $assets['script'] ) ) {
				$script = $assets['script'];

				wp_register_script(
					"404-to-301-$handle",
					empty( $script['external'] ) ? DUCKDEV_404_URL . 'app/assets/' . $script['src'] : $script['src'], // If external treat the source as full URL.
					$script['dependencies'] ?? array(),
					$script['version'] ?? DUCKDEV_404_VERSION,
					$script['footer'] ?? true
				);
			}

			// Register CSS files.
			if ( isset( $assets['style'] ) ) {
				$style = $assets['style'];

				wp_register_style(
					"404-to-301-$handle",
					empty( $style['external'] ) ? DUCKDEV_404_URL . 'app/assets/' . $style['src'] : $style['src'], // If external treat the source as full URL.
					$style['dependencies'] ?? array(),
					$style['version'] ?? DUCKDEV_404_VERSION,
					$style['media'] ?? 'all' // The media for which this stylesheet has been defined.
				);
			}
		}
	}

	/**
	 * Get the scripts list to register.
	 *
	 * This function will include all scripts
	 * added using 404_to_301_assets_get_scripts filter.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_assets(): array {
		$assets = array();

		// Plugin scripts.
		foreach ( array( 'settings', 'logs', 'redirects' ) as $handle ) {
			// Get auto generated asset data.
			$asset_data = $this->get_asset_data( $handle );
			// Style dependencies.
			$style_dependencies = array();

			// Handle style dependencies differently.
			if ( ! empty( $asset_data['dependencies'] ) ) {
				foreach ( $asset_data['dependencies'] as $dependency ) {
					// Use only if style is registered.
					if ( wp_style_is( $dependency, 'registered' ) ) {
						$style_dependencies[] = $dependency;
					}
				}
			}

			$assets[ $handle ] = array(
				'script' => array(
					'src'          => "$handle.js",
					'dependencies' => $asset_data['dependencies'],
					'version'      => $asset_data['version'],
				),
				'style'  => array(
					'src'          => "$handle.css",
					'dependencies' => $style_dependencies,
					'version'      => $asset_data['version'],
				),
			);
		}

		/**
		 * Filter to include/exclude new script.
		 *
		 * Modules should use this filter so that common
		 * localized vars will be available.
		 *
		 * @since 4.0.0
		 *
		 * @param array $assets Asset list.
		 */
		return apply_filters( '404_to_301_assets_get_assets', $assets );
	}

	/**
	 * Get asset data for a page.
	 *
	 * @since 4.0
	 *
	 * @param string $handle Asset handle.
	 *
	 * @return array
	 */
	private function get_asset_data( string $handle ): array {
		$data       = array();
		$asset_file = DUCKDEV_404_DIR . "app/assets/{$handle}.asset.php";

		// Load asset data from auto generated file.
		if ( file_exists( $asset_file ) ) {
			$data = require $asset_file;
		}

		return wp_parse_args(
			$data,
			array(
				'dependencies' => array(),
				'version'      => DUCKDEV_404_VERSION,
			)
		);
	}
}
