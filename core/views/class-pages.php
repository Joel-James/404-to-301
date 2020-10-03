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

use DuckDev\Redirect\Utils\Abstracts\View;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Pages extends View {

	/**
	 * Register the menu for the error logs page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function logs() {
		echo '<div id="404-to-301-logs"></div>';
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function settings() {
		echo '<div id="404-to-301-settings"></div>';
	}
}
