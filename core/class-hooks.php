<?php

namespace DuckDev404\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Core\Controllers\Admin\General;
use DuckDev404\Core\Controllers\Admin\Review;
use DuckDev404\Core\Utils\Abstracts\Base;
use DuckDev404\Core\Controllers\Common\I18n;
use DuckDev404\Core\Controllers\Admin\Assets;
use DuckDev404\Core\Controllers\Admin\Menu;

/**
 * Defines all actions and filters of the plugin.
 *
 * @note   Only hooks fired after the init hook will work here.
 *       You need to register earlier hooks separately.
 *
 * @link   http://duckdev.com
 * @since  4.0.0
 *
 * @author Joel James <joel@incsub.com>
 */
class Hooks extends Base {

	/**
	 * Setup all hooks for the plugin.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function setup() {
		// Common hooks.
		$this->common();

		// Front end hooks.
		$this->front();

		// Admin hooks.
		$this->admin();

		// Ajax requests.
		$this->ajax();
	}

	/**
	 * Register all the hooks required everywhere.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function common() {
		$i18n = I18n::get();

		add_action( 'plugins_loaded', [ $i18n, 'load_textdomain' ] );
	}

	/**
	 * Register all the hooks required for front end.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function front() {

	}

	/**
	 * Register all the hooks required for admin side.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function admin() {
		$general = General::get();
		$assets  = Assets::get();
		$menu    = Menu::get();
		$review  = Review::get();

		// Asset hooks.
		add_action( 'admin_enqueue_scripts', [ $assets, 'register_admin' ] );

		// Menu hooks.
		add_action( 'admin_menu', [ $menu, 'admin_menu' ], 99 );
		add_action( 'admin_menu', [ $menu, 'rename_menu' ], 100 );

		// General actions.
		add_action( 'admin_init', [ $general, 'register_settings' ] );
		add_filter( 'plugin_action_links', [ $general, 'action_links' ], 10, 2 );

		// Show review request.
		add_action( 'admin_notices', [ $review, 'notice' ] );
		add_action( 'admin_init', [ $review, 'action' ] );
	}

	/**
	 * Register all ajax functions.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function ajax() {

	}
}