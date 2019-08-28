<?php

namespace DuckDev\WP404\Controllers\Front;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The email notification functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Email extends Base {

	/**
	 * Redirect the page to custom page.
	 *
	 * @param int $type Redirect type.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function process() {

	}
}
