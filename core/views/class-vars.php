<?php

namespace DuckDev\WP404\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\WP404\Helpers;
use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The locale of the plugin.
 *
 * Loading page specific views are handled in this class.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Vars extends Base {

	/**
	 * Initialize assets functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function init() {
		// Localization.
		add_filter( '404_to_301_script_vars', [ $this, 'common' ] );
	}

	/**
	 * Set localized script vars for the assets.
	 *
	 * This is the common vars available in all scripts.
	 *
	 * @param array $vars Existing vars.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function common( $vars ) {
		// Localized strings.
		$vars['rest_nonce'] = wp_create_nonce( 'wp_rest' );
		$vars['rest_url']   = rest_url( '404-to-301/v1/' );
		$vars['settings']   = Helpers\Settings::get_options();

		return $vars;
	}
}