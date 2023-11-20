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

namespace RedirectPress\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use RedirectPress\Plugin;
use RedirectPress\Utils\Base;

/**
 * Class Assets
 *
 * @since   4.0.0
 * @extends Base
 * @package RedirectPress\Admin
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
		add_action( 'redirectpress_enqueue_assets_logs', array( $this, 'logs_assets' ) );
		add_action( 'redirectpress_enqueue_assets_settings', array( $this, 'settings_assets' ) );
		add_action( 'redirectpress_enqueue_assets_redirects', array( $this, 'redirects_assets' ) );
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
	public function assets( $hook_suffix ) {
		// Register assets.
		$this->register_styles();
		$this->register_scripts();

		// Setup enqueue actions only for our pages.
		$this->do_enqueue_action( $hook_suffix );
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
	private function do_enqueue_action( $hook_suffix ) {
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
			do_action( "redirectpress_enqueue_assets_{$page}", $page, $hook_suffix );

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
			do_action( 'redirectpress_enqueue_assets', $page, $hook_suffix );
		}
	}

	/**
	 * Register available styles.
	 *
	 * We are just registering the assets with WP now.
	 * We will enqueue them when it's really required.
	 * To enqueue a script use Assets::enqueue_style().
	 *
	 * @since  4.0.0
	 * @access private
	 * @see    Assets::enqueue_style().
	 * @uses   wp_register_style()
	 *
	 * @return void
	 */
	private function register_styles() {
		// Get all styles.
		$styles = $this->get_styles();

		// Register all styles.
		foreach ( $styles as $handle => $data ) {
			// If external treat the source as full URL.
			$src = empty( $data['external'] ) ? REDIRECTPRESS_URL . 'app/assets/css/' . $data['src'] : $data['src'];

			wp_register_style(
				$handle, // Style name.
				$src, // Source url.
				empty( $data['deps'] ) ? array() : $data['deps'], // Dependencies.
				empty( $data['version'] ) ? REDIRECTPRESS_VERSION : $data['version'], // Version number.
				empty( $data['media'] ) ? 'all' : $data['media'] // The media for which this stylesheet has been defined.
			);
		}
	}

	/**
	 * Register all provided scripts.
	 *
	 * We are just registering the scripts with WP now.
	 * We will enqueue them when it's really required.
	 * To enqueue a script use Assets::enqueue_script().
	 *
	 * @since  4.0.0
	 * @access private
	 * @see    Assets::enqueue_script().
	 * @uses   wp_register_script()
	 *
	 * @return void
	 */
	private function register_scripts() {
		// Get all scripts.
		$scripts = $this->get_scripts();

		// Register all available scripts.
		foreach ( $scripts as $handle => $data ) {
			// If external treat the source as full URL.
			$src = empty( $data['external'] ) ? REDIRECTPRESS_URL . 'app/assets/js/' . $data['src'] : $data['src'];

			wp_register_script(
				$handle, // Script name.
				$src, // Source URL.
				empty( $data['deps'] ) ? array() : $data['deps'], // Dependencies.
				empty( $data['version'] ) ? REDIRECTPRESS_VERSION : $data['version'], // Version number.
				isset( $data['footer'] ) ? $data['footer'] : true // Should enqueue in footer.
			);
		}
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
	public function enqueue_script( $script ) {
		// Only if not enqueued already.
		if ( ! wp_script_is( $script ) ) {
			// Script vars.
			wp_localize_script(
				$script,
				'redirectpress',
				/**
				 * Filter to add/remove vars in script.
				 *
				 * @since 4.0.0
				 *
				 * @param array $vars Localize vars.
				 */
				apply_filters( "redirectpress_assets_vars_{$script}", array() )
			);

			// Enqueue script now.
			wp_enqueue_script( $script );

			// Javascript translations.
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations(
					$script,
					'404-to-301',
					REDIRECTPRESS_DIR . '/languages/'
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
	public function enqueue_style( $style ) {
		// Only if not enqueued already.
		if ( ! wp_style_is( $style ) ) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * Get the scripts list to register.
	 *
	 * This function will include all scripts
	 * added using redirectpress_assets_get_scripts filter.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_scripts() {
		$scripts = array(
			// Logs scripts.
			'redirectpress-logs'      => array(
				'src'  => 'logs.min.js',
				'deps' => array( 'jquery', 'wp-i18n' ),
			),
			// Setting scripts.
			'redirectpress-settings'  => array(
				'src'  => 'settings.min.js',
				'deps' => array( 'jquery', 'wp-i18n' ),
			),
			// Redirects scripts.
			'redirectpress-redirects' => array(
				'src'  => 'redirects.min.js',
				'deps' => array( 'jquery', 'wp-i18n' ),
			),
		);

		/**
		 * Filter to include/exclude new script.
		 *
		 * Modules should use this filter so that common
		 * localized vars will be available.
		 *
		 * @since 4.0.0
		 *
		 * @param array $scripts Scripts list.
		 */
		return apply_filters( 'redirectpress_assets_get_scripts', $scripts );
	}

	/**
	 * Get the styles list to register.
	 *
	 * This function will include all scripts
	 * added using redirectpress_assets_get_scripts filter.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_styles() {
		$styles = array(
			// Logs styles.
			'redirectpress-logs'      => array(
				'src'  => 'logs.min.css',
				'deps' => array( 'wp-components' ),
			),
			// Settings styles.
			'redirectpress-settings'  => array(
				'src'  => 'settings.min.css',
				'deps' => array( 'wp-components' ),
			),
			// Settings styles.
			'redirectpress-redirects' => array(
				'src' => 'redirects.min.css',
			),
		);

		/**
		 * Filter to include/exclude new style.
		 *
		 * Modules should use this filter to include styles.
		 *
		 * @since 4.0.0
		 *
		 * @param array $styles Styles list.
		 */
		return apply_filters( 'redirectpress_assets_get_styles', $styles );
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
		$this->enqueue_script( 'redirectpress-logs' );
		$this->enqueue_style( 'redirectpress-logs' );
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
		$this->enqueue_script( 'redirectpress-settings' );
		$this->enqueue_style( 'redirectpress-settings' );
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
		$this->enqueue_script( 'redirectpress-redirects' );
		$this->enqueue_style( 'redirectpress-redirects' );
	}
}
