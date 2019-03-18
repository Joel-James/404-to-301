<?php

namespace DuckDev404\Inc\Front\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Core\Base;

/**
 * The redirect functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class General extends Base {

	/**
	 * Redirect url for current page.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Redirect status for current page.
	 *
	 * @since 4.0
	 *
	 * @var int
	 */
	protected $status = 301;

	/**
	 * Initialize the redirect functionality.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function init() {

	}

	/**
	 * Redirect the page to custom page.
	 * 
	 * @since 4.0
	 *
	 * @return void
	 */
	public function run() {
		// Redirect to custom page.
		if ( ! empty( $this->url ) ) {
			wp_redirect( $this->url, $this->status );
			// We need to exit because WP is lazy.
			exit;
		}
	}
}
