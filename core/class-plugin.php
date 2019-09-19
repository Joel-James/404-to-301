<?php

namespace DuckDev\WP404;

// Direct hit? Rest in peace.
defined( 'WPINC' ) || die;

use DuckDev\WP404\Controllers\Admin;
use DuckDev\WP404\Controllers\Common;
use DuckDev\WP404\Controllers\Endpoints;
use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The core plugin class.
 *
 * Defines everything we need for the plugin. That's why it is named 'Plugin'.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
final class Plugin extends Base {

	/**
	 * Initialize functionality of the plugin.
	 *
	 * This is where we kick-start the plugin by defining
	 * everything required and register all hooks.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	protected function __construct() {
		parent::__construct();

		// Run the plugin.
		$this->common();
		$this->front();
		$this->admin();
	}

	/**
	 * Initialize the hooks.
	 *
	 * Set activation and deactivation hooks here.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	public function activate() {
		// The code that runs during plugin activation.
	}

	/**
	 * Register all the hooks.
	 *
	 * Register all actions and filers with WordPress.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function common() {
		Common\I18n::get();
		Endpoints\Settings::get();
	}

	/**
	 * Register all the hooks.
	 *
	 * Register all actions and filers with WordPress.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function front() {}

	/**
	 * Register all the hooks.
	 *
	 * Register all actions and filers with WordPress.
	 *
	 * @since  4.0
	 * @access private
	 *
	 * @return void
	 */
	private function admin() {
		Admin\General::get();
		Admin\Assets::get();
		Admin\Menu::get();
		Admin\Review::get();
	}
}
