<?php
/**
 * The plugin script vars class.
 *
 * This class managed the localized script vars for different
 * scripts used by the plugin.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    Admin
 * @subpackage Vars
 */

namespace RedirectPress\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use RedirectPress\Data;
use RedirectPress\Utils\Base;

/**
 * Class Vars
 *
 * @since   4.0.0
 * @extends Base
 * @package RedirectPress\Admin
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
		add_action( 'redirectpress_assets_vars_redirectpress-logs', array( $this, 'logs' ) );
		add_action( 'redirectpress_assets_vars_redirectpress-settings', array( $this, 'settings' ) );
		add_action( 'redirectpress_assets_vars_redirectpress-redirects', array( $this, 'redirects' ) );
	}

	/**
	 * Add the vars only required in settings script.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $vars Script vars.
	 *
	 * @return array $vars
	 */
	public function settings( $vars ) {
		// Include common vars.
		$vars = $this->common( $vars );

		$vars['types'] = Data::redirect_types();
		$vars['pages'] = Data::pages();

		return $vars;
	}

	/**
	 * Add the vars only required in logs script.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $vars Script vars.
	 *
	 * @return array $vars
	 */
	public function logs( $vars ) {
		// Include common vars.
		$vars = $this->common( $vars );

		return $vars;
	}

	/**
	 * Add the vars only required in redirects script.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $vars Script vars.
	 *
	 * @return array $vars
	 */
	public function redirects( $vars ) {
		// Include common vars.
		$vars = $this->common( $vars );

		$vars['test'] = '';

		return $vars;
	}

	/**
	 * Add the vars common for all scripts.
	 *
	 * Make sure to limit the items which are only needed in all
	 * our scripts. Use separate method for individual scripts.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $vars Script vars.
	 *
	 * @return array $vars
	 */
	private function common( $vars ) {
		// Rest API data.
		$vars['rest'] = array(
			'base'  => rest_url( '404-to-301/v1/' ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		);

		// Settings data.
		$vars['settings'] = redirectpress_settings()->all();

		// Common items.
		$vars['version'] = REDIRECTPRESS_VERSION;

		return $vars;
	}
}
