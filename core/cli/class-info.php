<?php
/**
 * The plugin info command class.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    CLI
 * @subpackage Info
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Plugin;
use DuckDev\QueryBuilder\Query;

/**
 * Class Info
 *
 * Plugin information CLI command.
 *
 * @package DuckDev\Redirect\CLI
 * @since   4.0.0
 */
class Info extends Command {

	/**
	 * Get the plugin information.
	 *
	 * Show the basic plugin information such as
	 * name, slug, version etc.
	 *
	 * @param array $args  Command arguments.
	 * @param array $extra Extra options.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function command( $args, $extra ) {
		// Get available info.
		$info = $this->get_info();

		// Item key.
		$item = $this->get_arg( $extra, 'item' );

		// Asking for single item.
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
	 * Use dd4t3_cli_get_info filter to add more data
	 * to the information list.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_info() {
		$info = array(
			'name'    => Plugin::name(),
			'slug'    => Plugin::slug(),
			'version' => DD4T3_VERSION,
		);

		/**
		 * Filter to modify plugin info data for CLI.
		 *
		 * @param array $info Info.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_cli_get_info', $info );
	}
}
