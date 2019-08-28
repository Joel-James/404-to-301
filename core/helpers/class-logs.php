<?php

namespace DuckDev\WP404\Helpers;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

/**
 * Define the logs functionality.
 *
 * Error logs, site logs and more for the DD Boilerplate.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Logs {

	/**
	 * Log something to PHP error log.
	 *
	 * @param mixed $data  Log data (array or string).
	 * @param bool  $force Should force log even if WP_DEBUG is not enabled?
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return void
	 */
	public static function error_log( $data, $force = false ) {
		// If debug is enabled, add to error log.
		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || $force ) {
			// Log the content using print_r().
			error_log( print_r( $data, true ) );
		}
	}
}
