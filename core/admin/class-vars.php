<?php
/**
 * The plugin script vars class.
 *
 * This class managed the localized script vars for different
 * scripts used by the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Admin
 * @subpackage Vars
 */

namespace DuckDev\Redirect\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class Vars
 *
 * @package DuckDev\Redirect\Admin
 * @since   4.0.0
 */
class Vars extends Base {

	/**
	 * Initialize the class.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		// Add required vars for javascript.
		add_action( 'dd404_assets_vars_dd404-logs', array( $this, 'common' ) );
		add_action( 'dd404_assets_vars_dd404-logs', array( $this, 'logs' ) );
		add_action( 'dd404_assets_vars_dd404-settings', array( $this, 'common' ) );
		add_action( 'dd404_assets_vars_dd404-settings', array( $this, 'settings' ) );
	}

	/**
	 * Add the vars common for all scripts.
	 *
	 * @param array $vars Script vars.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array $vars
	 */
	public function common( $vars ) {
		// Rest API data.
		$vars['rest'] = array(
			'base'  => rest_url( '404-to-301/v1/' ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		);

		// Settings data.
		$vars['settings'] = dd404_settings()->get_settings();

		return $vars;
	}

	/**
	 * Add the vars only required in settings script.
	 *
	 * @param array $vars Script vars.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array $vars
	 */
	public function settings( $vars ) {
		return $vars;
	}

	/**
	 * Add the vars only required in logs script.
	 *
	 * @param array $vars Script vars.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array $vars
	 */
	public function logs( $vars ) {
		return $vars;
	}
}
