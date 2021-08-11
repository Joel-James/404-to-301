<?php
/**
 * The plugin permissions class.
 *
 * This class contains the functionality to manage the permissions
 * inside the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Permission
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Permission
 *
 * @package DuckDev\Redirect\Controllers
 */
class Data {

	/**
	 * Get available redirect types.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void
	 */
	public static function redirect_types() {
		$types = array(
			301 => __( '301 - Moved Permanently', '404-to-301' ),
			302 => __( '302 - Found', '404-to-301' ),
			303 => __( '303 - See Other', '404-to-301' ),
			304 => __( '304 - Not Modified', '404-to-301' ),
			307 => __( '307 - Temporary Redirect', '404-to-301' ),
			308 => __( '308 - Permanent Redirect', '404-to-301' ),
		);

		/**
		 * Filter to add or remove redirect types.
		 *
		 * @param array $types Redirect types.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_redirect_types', $types );
	}
}
