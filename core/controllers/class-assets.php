<?php
/**
 * The plugin assets class.
 *
 * This class contains the functionality to manage the assets
 * inside the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Assets
 */

namespace DuckDev\Redirect\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Controller;

/**
 * Class Permission
 *
 * @package DuckDev\Redirect\Controllers
 */
class Assets extends Controller {

	/**
	 * Initialize assets functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'public_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	/**
	 * Assets for our front end functionality.
	 *
	 * Currently this function will not register anything.
	 * But this should be here for other modules to register
	 * public assets.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function public_assets() {
		$this->register_styles( false );
		$this->register_scripts( false );
	}

	/**
	 * Assets for our front end functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function admin_assets() {
		$this->register_styles();
		$this->register_scripts();
	}

	/**
	 * Register available styles.
	 *
	 * We are just registering the assets with WP now.
	 * We will enqueue them when it's really required.
	 *
	 * @param bool $admin Is admin assets?.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	private function register_styles( $admin = true ) {
		// Get all the assets.
		$styles = $this->get_styles( $admin );

		// Register all styles.
		foreach ( $styles as $handle => $data ) {
			// Get the source full url.
			$src = empty( $data['external'] ) ? DD4T3_URL . 'app/assets/css/' . $data['src'] : $data['src'];

			// Register custom videos scripts.
			wp_register_style(
				$handle,
				$src,
				empty( $data['deps'] ) ? array() : $data['deps'],
				empty( $data['version'] ) ? DD4T3_VERSION : $data['version'],
				! empty( $data['media'] )
			);
		}

		/**
		 * Action hook to run something when we are on settings page.
		 *
		 * This hook can be used to add new settings menu items.
		 *
		 * @param bool $admin Menu string.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_after_register_styles', $admin );
	}

	/**
	 * Register available scripts.
	 *
	 * We are just registering the assets with WP now.
	 * We will enqueue them when it's really required.
	 *
	 * @param bool $admin Is admin assets?.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	private function register_scripts( $admin = true ) {
		// Get all the assets.
		$scripts = $this->get_scripts( $admin );

		// Register all available scripts.
		foreach ( $scripts as $handle => $data ) {
			// Get the source full url.
			$src = empty( $data['external'] ) ? DD4T3_URL . 'app/assets/js/' . $data['src'] : $data['src'];

			// Register custom videos scripts.
			wp_register_script(
				$handle,
				$src,
				empty( $data['deps'] ) ? array() : $data['deps'],
				empty( $data['version'] ) ? DD4T3_VERSION : $data['version'],
				isset( $data['footer'] ) ? $data['footer'] : true
			);
		}

		/**
		 * Action hook to run something when we are on settings page.
		 *
		 * This hook can be used to add new settings menu items.
		 *
		 * @param bool $admin Menu string.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_after_register_scripts', $admin );
	}

	/**
	 * Enqueue a script with localization.
	 *
	 * Always use this method to enqueue scripts. Then only
	 * we will get the required localized vars.
	 *
	 * @param string $script Script handle name.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function enqueue_script( $script ) {
		// Only if not enqueued already.
		if ( ! wp_script_is( $script ) ) {
			// Script vars.
			wp_localize_script(
				$script,
				'dd404vars',
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

			/**
			 * Action hook to run something when we are on settings page.
			 *
			 * This hook can be used to add new settings menu items.
			 *
			 * @param string $script Menu string.
			 *
			 * @since 4.0
			 */
			do_action( 'dd404_after_enqueue_script', $script );
		}
	}

	/**
	 * Enqueue a style with WordPress.
	 *
	 * This is just an alias function.
	 *
	 * @param string $style Style handle name.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function enqueue_style( $style ) {
		// Only if not enqueued already.
		if ( ! wp_style_is( $style ) ) {
			wp_enqueue_style( $style );

			/**
			 * Action hook to run something when we are on settings page.
			 *
			 * This hook can be used to add new settings menu items.
			 *
			 * @param string $script Menu string.
			 *
			 * @since 4.0
			 */
			do_action( 'dd404_after_enqueue_style', $style );
		}
	}

	/**
	 * Get the scripts list to register.
	 *
	 * @param bool $admin Is admin assets?.
	 *
	 * @since 3.2.4
	 *
	 * @return array
	 */
	private function get_scripts( $admin = true ) {
		$scripts = array();

		/**
		 * Filter to include/exclude new script.
		 *
		 * Modules should use this filter to that common localized
		 * vars will be available.
		 *
		 * @param array $scripts Scripts list.
		 * @param bool  $admin   Is admin assets?.
		 *
		 * @since 3.2.4
		 */
		return apply_filters( 'dd404_assets_get_scripts', $scripts, $admin );
	}

	/**
	 * Get the styles list to register.
	 *
	 * @param bool $admin Is admin assets?.
	 *
	 * @since 3.2.4
	 *
	 * @return array
	 */
	private function get_styles( $admin = true ) {
		$styles = array();

		/**
		 * Filter to include/exclude new style.
		 *
		 * Modules should use this filter to include styles.
		 *
		 * @param array $styles Styles list.
		 * @param bool  $admin  Is admin assets?.
		 *
		 * @since 3.2.4
		 */
		return apply_filters( 'dd404_assets_get_styles', $styles, $admin );
	}
}
