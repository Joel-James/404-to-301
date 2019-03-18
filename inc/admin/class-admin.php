<?php

namespace DuckDev404\Inc\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Inc\Core\Base;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Admin extends Base {

	/**
	 * Admin menu class instance.
	 *
	 * @var Menu
	 *
	 * @since  4.0
	 * @access protected
	 */
	protected $menu;

	/**
	 * Admin pages class instance.
	 *
	 * @var Page
	 *
	 * @since  4.0
	 * @access protected
	 */
	protected $page;

	/**
	 * Admin assets class instance.
	 *
	 * @var Menu
	 *
	 * @since  4.0
	 * @access protected
	 */
	protected $assets;

	/**
	 * Set up child classes.
	 *
	 * Set class properties and initialize child classes.
	 *
	 * @since 4.0
	 */
	protected function init() {
		$this->menu   = Menu::instance();
		$this->assets = Assets::instance();
		$this->page   = Page::instance();
	}
}
