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

use DuckDev\Redirect\Abstracts\Base;

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
	 * @return array
	 */
	public function boot() {
		return array();
	}
}
