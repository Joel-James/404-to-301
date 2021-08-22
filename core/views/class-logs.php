<?php
/**
 * The plugin logs page view class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Logs
 *
 * @extends View
 * @since   4.0.0
 * @package DuckDev\Redirect\Views
 */
class Logs extends View {

	/**
	 * Content for logs page.
	 *
	 * Render the template file for error logs page with data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function content() {
		// Admin logs template.
		$this->render( 'logs' );
	}
}
