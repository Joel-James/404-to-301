<?php

namespace DuckDev404\Inc\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Helpers\Settings;

/**
 * The core functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Core extends Base {

	/**
	 * Plugin setting values.
	 *
	 * @since 4.0
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Register hoosk from class and children.
	 *
	 * @since 4.0
	 */
	public function init() {
		// Get the settings.
		$this->settings = Settings::get_options();
	}
}
