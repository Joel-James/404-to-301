<?php
/**
 * The core plugin class.
 *
 * This is the main class that initialize the entire plugin functionality.
 * Only one instance of this class be created.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Core
 */

namespace RedirectPress;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use RedirectPress\Utils\Base;
use RedirectPress\Database\Database;

/**
 * Class Core
 *
 * @since   4.0.0
 * @package RedirectPress
 * @extends Base
 */
final class Core extends Base {

	/**
	 * Boot and start the plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	protected function init() {
		$this->common();
		$this->admin();
		$this->front();
		$this->api();
		$this->cli();

		/**
		 * Action hook to execute after plugin is loaded.
		 *
		 * Addons/extensions can use this to initialize the plugin
		 * so the addons/extensions will be loaded only if the required
		 * parent plugin is active.
		 *
		 * @since 4.0.0
		 *
		 * @param Core $this Plugin core instance.
		 */
		do_action( 'redirectpress_init', $this );
	}

	/**
	 * Setup all classes for that is common.
	 *
	 * These classes are required for both front end and
	 * back end of WordPress.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function common() {
		Database::instance();
		Settings::instance();
	}

	/**
	 * Setup all classes for the admin functionality.
	 *
	 * These classes are required only on admin side.
	 *
	 * @since  4.0.0
	 * @access public
	 * @uses   is_admin()
	 *
	 * @return void
	 */
	private function admin() {
		if ( is_admin() ) {
			Admin\Menu::instance();
			Admin\Assets::instance();
			Admin\Vars::instance();
			Views\Admin::instance();
		}
	}

	/**
	 * Setup all classes for the front end functionality.
	 *
	 * These classes are required only on front end.
	 *
	 * @since  4.0.0
	 * @access public
	 * @uses   is_admin()
	 *
	 * @return void
	 */
	private function front() {
		if ( ! is_admin() ) {
			Front::instance();
		}
	}

	/**
	 * Setup plugin API endpoints.
	 *
	 * Rest API endpoints are used to communicate with WP
	 * from UI or from other external sources.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function api() {
		new Api\Logs();
		new Api\Data();
		new Api\Redirects();
		new Api\Settings();
	}

	/**
	 * Setup plugin WP CLI commands.
	 *
	 * WP CLI commands are added only if the WP CLI
	 * is available and running.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function cli() {
		// Add commands only if CLI available.
		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( '\WP_CLI' ) ) {
			\WP_CLI::add_command(
				'404-to-301',
				'\RedirectPress\CLI\CLI',
				array(
					'shortdesc' => __( 'Manage settings, error logs and redirects from 404 to 301.', '404-to-301' ),
				)
			);
		}
	}
}
