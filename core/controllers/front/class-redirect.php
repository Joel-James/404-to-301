<?php

namespace DuckDev404\Core\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Core\Core\Base;

/**
 * The redirect functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Redirect extends Base {

	/**
	 * Redirect class object.
	 *
	 * @var object
	 */
	protected $redirect;

	/**
	 * Initialize the redirect functionality.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function init() {
		$settings = duckdev_404()->core()->settings;

		switch ( $settings['type'] ) {
			case 404:
				$this->redirect = Redirect\_404::instance();
				break;

			default:
				$this->redirect = Redirect\General::instance();
		}
	}
}
