<?php
/**
 * The core plugin class.
 *
 * This is the main class that initialize the entire plugin functionality.
 * Only one instance of this class be created.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Core
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_CLI;
use WP_CLI\Utils;
use DuckDev\Redirect\Plugin;

/**
 * Class CLI
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Info extends CLI {

	/**
	 * Get the plugin information.
	 *
	 * ## OPTIONS
	 *
	 * [--item=<value>]
	 * : The info item key (name, slug, version).
	 *
	 * ## EXAMPLES
	 *
	 *     wp 404-to-301 info --item=<value>
	 *
	 * @param array $args Command arguments.
	 *                    @param array $extra Extra options.
	 *
	 * @return void
	 */
	public function info( $args, $extra ) {
		// Get available info.
		$info = $this->get_info();

		// Check if asked only for specific item.
		$item = $this->get_arg( $extra, 'item' );

		if ( $item && isset( $info[ $item ] ) ) {
			$this->show( $info[ $item ] );
		} else {
			// Show info as table.
			$this->maybe_as_table( $info );
		}
	}

	/**
	 * Get the plugin info data.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_info() {
		$info = array(
			'name'    => Plugin::instance()->name(),
			'slug'    => Plugin::instance()->slug(),
			'version' => DD404_VERSION,
		);

		/**
		 * Filter to modify plugin info data for CLI.
		 *
		 * @param array $info Info.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_cli_get_info', $info );
	}
}
