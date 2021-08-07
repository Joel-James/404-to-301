<?php
/**
 * The plugin pages view class.
 *
 * This class handles the admin pages views for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Pages
 */

namespace DuckDev\Redirect\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Logs extends View {

	/**
	 * Register the menu for the error logs page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function content() {
		/**
		 * Action hook to run something after rendering logs page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_before_admin_pages_logs_render' );

		// Admin logs template.
		$this->render( 'logs' );

		/**
		 * Action hook to run something after rendering logs page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_logs_render' );
	}
}
