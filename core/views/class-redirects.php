<?php
/**
 * The plugin redirects list page view class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Redirects
 */

namespace RedirectPress\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Redirects
 *
 * @extends View
 * @since   4.0.0
 * @package RedirectPress\Views
 */
class Redirects extends View {

	/**
	 * Content for redirects page.
	 *
	 * Render the template file for redirects page with data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function content() {
		// Admin redirects template.
		$this->render( 'redirects' );
	}
}
