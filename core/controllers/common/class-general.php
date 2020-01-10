<?php

namespace DuckDev\WP404\Controllers\Common;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The general functionality of the common plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class General extends Base {

	/**
	 * Initilize the class by registering the hooks.
	 *
	 * @since 4.0.0
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_tables' ] );
	}

	/**
	 * Registering custom tables with ORM.
	 *
	 * This function is used to register our custom table to WP ORM.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_tables() {
		//Manager::register( new Log() );
	}
}
