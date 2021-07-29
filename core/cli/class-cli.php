<?php
/**
 * The base class for CLI commands.
 *
 * Extend this class to add new command classes so that the
 * helper functions can be easily used.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    CLI
 * @subpackage CLI
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_CLI;
use WP_CLI\Utils;
use WP_CLI_Command;

/**
 * Class CLI
 *
 * @package DuckDev\Redirect\CLI
 * @since   4.0.0
 */
abstract class CLI extends WP_CLI_Command {

	/**
	 * Create new instance of CLI command.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return static Called class instance.
	 */
	public static function instance() {
		static $instances = array();

		// @codingStandardsIgnoreLine Plugin-backported
		$called_class_name = get_called_class();

		// Only if not already exist.
		if ( ! isset( $instances[ $called_class_name ] ) ) {
			$instances[ $called_class_name ] = new $called_class_name();

			// Add the command.
			WP_CLI::add_command( '404-to-301', $called_class_name );
		}

		return $instances[ $called_class_name ];
	}

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
	 * Display a message to CLI.
	 *
	 * @param string $message Message.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function log( $message ) {
		WP_CLI::log( $message );
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
}
