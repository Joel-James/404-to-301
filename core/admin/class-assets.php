<?php
/**
 * The plugin assets class.
 *
 * This class contains the functionality to manage the assets
 * inside the plugin admin screens.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Assets
 */

namespace DuckDev\Redirect\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Data;
use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class Assets
 *
 * @package DuckDev\Redirect\Admin
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
		add_action( 'dd404_enqueue_assets_logs', array( $this, 'logs_assets' ) );
		add_action( 'dd404_enqueue_assets_settings', array( $this, 'settings_assets' ) );
	}

	/**
	 * Assets for our front end functionality.
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @since  4.0.0
	 * @access public
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
	 * Assets for our front end functionality.
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function do_enqueue_action( $hook_suffix ) {
		// Check if current page is one of our pages.
		$page = array_search( $hook_suffix, Data\Page::PAGES, true );

		// If our page.
		if ( ! empty( $page ) ) {
			/**
			 * Action hook to enqueue assets for our pages.
			 *
			 * Use this hook to enqueue scripts and styles which are
			 * only loaded on our plugins pages. This hook will be
			 * fired only on specified page.
			 *
			 * @param string $page        Current page key.
			 * @param string $hook_suffix The current admin page.
			 *
			 * @since 4.0.0
			 */
			do_action( "dd404_enqueue_assets_{$page}", $page, $hook_suffix );

			/**
			 * Action hook to enqueue assets for our pages.
			 *
			 * This hooks will be fired for all plugin pages.
			 *
			 * @param string $page        Current page key.
			 * @param string $hook_suffix The current admin page.
			 *
			 * @since 4.0.0
			 */
			do_action( 'dd404_enqueue_assets', $page, $hook_suffix );
		}
	}

	/**
	 * Register available styles.
	 *
	 * We are just registering the assets with WP now.
	 * We will enqueue them when it's really required.
	 * To enqueue a script @see Assets::enqueue_style().
	 *
	 * @since  4.0.0
	 * @access private
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
			$src = empty( $data['external'] ) ? DD404_URL . 'app/assets/css/' . $data['src'] : $data['src'];

			wp_register_style(
				$handle, // Style name.
				$src, // Source url.
				empty( $data['deps'] ) ? array() : $data['deps'], // Dependencies.
				empty( $data['version'] ) ? DD404_VERSION : $data['version'], // Version number.
				empty( $data['media'] ) ? 'all' : $data['media'] // The media for which this stylesheet has been defined.
			);
		}
	}

	/**
	 * Register all provided scripts.
	 *
	 * We are just registering the scripts with WP now.
	 * We will enqueue them when it's really required.
	 * To enqueue a script @see Assets::enqueue_script().
	 *
	 * @since  4.0.0
	 * @access private
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
			$src = empty( $data['external'] ) ? DD404_URL . 'app/assets/js/' . $data['src'] : $data['src'];

			wp_register_script(
				$handle, // Script name.
				$src, // Source URL.
				empty( $data['deps'] ) ? array() : $data['deps'], // Dependencies.
				empty( $data['version'] ) ? DD404_VERSION : $data['version'], // Version number.
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
	 * @param string $script Script handle name.
	 *
	 * @since  4.0.0
	 * @access public
	 * @uses   wp_script_is()
	 * @uses   wp_localize_script()
	 * @uses   wp_enqueue_script()
	 * @uses   wp_set_script_translations()
	 *
	 * @return void
	 */
	public function enqueue_script( $script ) {
		// Only if not enqueued already.
		if ( ! wp_script_is( $script ) ) {
			// Script vars.
			wp_localize_script(
				$script,
				'dd404',
				/**
				 * Filter to add/remove vars in script.
				 *
				 * @param array $vars Localize vars.
				 *
				 * @since 4.0.0
				 */
				apply_filters( "dd404_assets_vars_{$script}", array() )
			);

			// Enqueue script now.
			wp_enqueue_script( $script );

			// Javascript translations.
			wp_set_script_translations(
				$script,
				'404-to-301',
				DD404_DIR . '/languages/'
			);
		}
	}

	/**
	 * Enqueue a style with WordPress.
	 *
	 * This is just an alias for wp_enqueue_style().
	 *
	 * @param string $style Style handle name.
	 *
	 * @since  4.0.0
	 * @access public
	 * @uses   wp_enqueue_style()
	 * @uses   wp_style_is()
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
	 * added using dd404_assets_get_scripts filter.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_scripts() {
		$scripts = array(
			// Logs scripts.
			'dd404-logs'     => array(
				'src' => 'logs.min.js',
			),
			// Settings scripts.
			'dd404-settings' => array(
				'src'  => 'settings.min.js',
				'deps' => array( 'wp-i18n' ),
			),
		);

		/**
		 * Filter to include/exclude new script.
		 *
		 * Modules should use this filter so that common
		 * localized vars will be available.
		 *
		 * @param array $scripts Scripts list.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_assets_get_scripts', $scripts );
	}

	/**
	 * Get the styles list to register.
	 *
	 * This function will include all scripts
	 * added using dd404_assets_get_scripts filter.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_styles() {
		$styles = array(
			// Logs styles.
			'dd404-logs'     => array(
				'src' => 'logs.min.css',
			),
			// Settings styles.
			'dd404-settings' => array(
				'src' => 'admin.min.css',
			),
		);

		/**
		 * Filter to include/exclude new style.
		 *
		 * Modules should use this filter to include styles.
		 *
		 * @param array $styles Styles list.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_assets_get_styles', $styles );
	}

	/**
	 * Enqueue assets for the logs page.
	 *
	 * Enqueue styles and scripts which are only
	 * required on error logs page.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function logs_assets() {
		$this->enqueue_script( 'dd404-logs' );
		$this->enqueue_style( 'dd404-logs' );
	}

	/**
	 * Enqueue assets for the settings page.
	 *
	 * Enqueue styles and scripts which are only
	 * required on error settings page.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function settings_assets() {
		$this->enqueue_script( 'dd404-settings' );
		$this->enqueue_style( 'dd404-settings' );
	}
}
