<?php
/**
 * Object template class.
 *
 * This class allows for templates for any object type, which includes `post`,
 * `term`, and `user`.  When viewing a particular single post, term archive, or
 * user/author archive page, the template can be used.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @subpackage Core
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Controllers;
use DuckDev\Redirect\Controllers\Admin;
use DuckDev\Redirect\Controllers\Front;
use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Creates a new object template.
 *
 * @since  5.0.0
 * @access public
 */
class Core extends Base {

	/**
	 * Boot and start the plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		$this->setup();

		/**
		 * Action hook to execute after plugin is loaded.
		 *
		 * Addon plugins can use this to initialize.
		 *
		 * @param Core $this Plugin instance.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_init', $this );
	}

	/**
	 * Boot and start the plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function setup() {
		$this->common();
		$this->admin();
		$this->front();
		$this->api();
	}

	/**
	 * Boot and start the plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function common() {
		Controllers\Assets::instance();
		Controllers\Settings::instance();
	}

	/**
	 * Boot and start the plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function admin() {
		Admin\Menu::instance();
		Admin\Assets::instance();
		Admin\Admin::instance();
	}

	/**
	 * Boot and start the plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function front() {
		Front\Main::instance();
		//Front\Actions\Log::instance();
		//Front\Actions\Email::instance();
		//Front\Actions\Redirect::instance();
	}

	/**
	 * Boot and start the plugin.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function api() {
		Api\Settings::instance();
	}
}
