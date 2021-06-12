<?php
/**
 * The plugin assets controller class.
 *
 * This class handles the admin assets functionality for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Assets
 */

namespace DuckDev\Redirect\Controllers\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Views;
use DuckDev\Redirect\Utils\Abstracts\Controller;
use DuckDev\Redirect\Controllers\Assets as Assets_Helper;

/**
 * Class Assets
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Assets extends Controller {

	/**
	 * Initialize the assets class and register the hooks.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'dd404_assets_get_scripts', array( $this, 'get_scripts' ), 10, 2 );
		add_filter( 'dd404_assets_get_styles', array( $this, 'get_styles' ), 10, 2 );

		add_action( 'dd404_after_admin_pages_logs_render', array( $this, 'logs_assets' ) );
		add_action( 'dd404_after_admin_pages_settings_render', array( $this, 'settings_assets' ) );
	}

	/**
	 * Get the scripts list to register.
	 *
	 * @param array $scripts Scripts list.
	 * @param bool  $admin   Is admin assets?.
	 *
	 * @since 3.2.4
	 *
	 * @return array
	 */
	public function get_scripts( $scripts, $admin ) {
		if ( $admin ) {
			// GA settings.
			$scripts['dd404-vendors'] = array(
				'src'  => 'chunk-vendors.min.js',
				'deps' => array(),
			);

			// GA settings.
			$scripts['dd404-logs'] = array(
				'src'  => 'logs.min.js',
				'deps' => array( 'dd404-vendors' ),
			);

			// GA settings.
			$scripts['dd404-general-settings'] = array(
				'src'  => 'general-settings.min.js',
				'deps' => array( 'dd404-vendors' ),
			);

			$scripts['dd404-redirect-settings'] = array(
				'src'  => 'redirect-settings.min.js',
				'deps' => array( 'dd404-vendors' ),
			);

			$scripts['dd404-logs-settings'] = array(
				'src'  => 'logs-settings.min.js',
				'deps' => array( 'dd404-vendors' ),
			);

			$scripts['dd404-notifications-settings'] = array(
				'src'  => 'notifications-settings.min.js',
				'deps' => array( 'dd404-vendors' ),
			);
		}

		return $scripts;
	}

	/**
	 * Get the styles list to register.
	 *
	 * @param array $styles Styles list.
	 * @param bool  $admin  Is admin assets?.
	 *
	 * @since 3.2.4
	 *
	 * @return array
	 */
	public function get_styles( $styles, $admin ) {
		if ( $admin ) {
			// GA settings.
			$styles['dd404-logs'] = array(
				'src' => 'logs.min.css',
			);

			// GA settings.
			$styles['dd404-general-settings'] = array(
				'src' => 'general-settings.min.css',
			);

			$styles['dd404-redirect-settings'] = array(
				'src' => 'redirect-settings.min.css',
			);

			$styles['dd404-logs-settings'] = array(
				'src' => 'logs-settings.min.css',
			);

			$styles['dd404-notifications-settings'] = array(
				'src' => 'notifications-settings.min.css',
			);
		}

		return $styles;
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function logs_assets() {
		Assets_Helper::instance()->enqueue_script( 'dd404-logs' );
		Assets_Helper::instance()->enqueue_style( 'dd404-logs' );

		/**
		 * Action hook to run something after enqueue logs assets.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_after_logs_assets' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function settings_assets() {
		$tab = Views\Settings::instance()->get_current_tab();

		$name = 'dd404-' . $tab . '-settings';

		Assets_Helper::instance()->enqueue_script( $name );
		Assets_Helper::instance()->enqueue_style( $name );

		/**
		 * Action hook to run something after enqueue logs assets.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_after_settings_assets' );
	}
}
