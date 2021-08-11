<?php
/**
 * The base class for CLI commands.
 *
 * Extend this class to add new command classes so that the
 * helper functions can be easily used.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    CLI
 * @subpackage Command
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_CLI;
use WP_CLI\Utils;
use DuckDev\Redirect\Utils\Base;

/**
 * Class Command
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\CLI
 */
abstract class Command extends Base {

	/**
	 * Display data as table if array.
	 *
	 * Only single normal array is allowed. If there are
	 * children array, it will be displayed as string.
	 *
	 * @param mixed $data   Data to display.
	 * @param array $format Format of items.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function maybe_as_table( $data, array $format = array( 'key', 'value' ) ) {
		// Only if array.
		if ( is_array( $data ) ) {
			$output = array();
			foreach ( $data as $key => $value ) {
				$output[] = array(
					$format[0] => $key,
					$format[1] => $value,
				);
			}

			// Show the output as table.
			Utils\format_items( 'table', $output, $format );
		} else {
			// Show normal output.
			$this->success( $data );
		}
	}

	/**
	 * Display a message to CLI and ignore --quiet.
	 *
	 * @param string $message Message.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function show( $message ) {
		WP_CLI::line( $message );
	}

	/**
	 * Display success message to CLI.
	 *
	 * @param string $message Success message.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function success( $message ) {
		WP_CLI::success( $message );
	}

	/**
	 * Display error message to CLI.
	 *
	 * @param string $message Error message.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function error( $message = '' ) {
		WP_CLI::error( empty( $message ) ? __( 'Invalid command.', '404-to-301' ) : $message );
	}

	/**
	 * Get an argument value from the command.
	 *
	 * @param array  $args    Arguments.
	 * @param string $key     Argument key.
	 * @param mixed  $default Default value.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return mixed
	 */
	protected function get_arg( $args, $key, $default = null ) {
		return Utils\get_flag_value( $args, $key, $default );
	}

	/**
	 * Setup progress bar for batch process.
	 *
	 * @param string $message Progress message.
	 * @param int    $count   Total items.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return Utils\make_progress_bar
	 */
	protected function make_progress( $message, $count ) {
		return Utils\make_progress_bar( $message, $count );
	}
}
