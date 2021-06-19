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

namespace DuckDev\Redirect\Controllers\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Controllers\Settings;
use DuckDev\Redirect\Utils\Abstracts\Controller;

/**
 * Class Permission
 *
 * @package DuckDev\Redirect\Controllers
 */
class Admin extends Controller {

	/**
	 * Initialize assets functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function init() {
		// Add required vars for javascript.
		add_action( 'dd404_assets_vars_dd404-logs-settings', array( $this, 'common_vars' ) );
		add_action( 'dd404_assets_vars_dd404-general-settings', array( $this, 'common_vars' ) );
		add_action( 'dd404_assets_vars_dd404-redirect-settings', array( $this, 'common_vars' ) );
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
	 * @return array $vars
	 */
	public function common_vars( $vars ) {
		// Rest data.
		$vars['rest'] = array(
			'base'  => rest_url( '404-to-301/v1/' ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		);

		// Settings data.
		$vars['settings'] = Settings::get_settings();

		return $vars;
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
	 * @return array $vars
	 */
	public function general_vars( $vars ) {
		// Add general settings data.
		return $vars;
	}
}
