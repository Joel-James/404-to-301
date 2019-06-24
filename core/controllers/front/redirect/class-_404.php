<?php

namespace DuckDev404\Core\Front\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * The redirect functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class _404 extends General {

	/**
	 * Redirect status for current page.
	 *
	 * @since 4.0
	 *
	 * @var int
	 */
	protected $status = 404;

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
	 * Run the redirect action.
	 * 
	 * Execute the redirect action for the 404
	 * errors. In this status, we will not redirect,
	 * instead we will show 404 header status with
	 * the custom page content.
	 * 
	 * @since 4.0
	 *
	 * @return void
	 */
	public function run() {

	}
}
