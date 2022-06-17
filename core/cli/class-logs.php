<?php
/**
 * The plugin logs command class.
 *
 * This class contains log management commands.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    CLI
 * @subpackage Logs
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Logs
 *
 * Plugin 404 logs CLI class.
 *
 * @since   4.0.0
 * @extends Command
 * @package DuckDev\Redirect\CLI
 */
class Logs extends Command {

	/**
	 * Manage plugin 404 logs.
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
		// Action.
		$action = $args[0];
		// Options.
		$limit    = $this->get_arg( $extra, 'limit', 100 );
		$order_by = $this->get_arg( $extra, 'orderby', 'log_id' );
		$order    = $this->get_arg( $extra, 'order', 'desc' );

		if ( 'get' === $action ) {
			$this->get_logs( $limit, $order_by, $order );
		} elseif ( 'clean' === $action ) {
			$this->clean_logs( $limit, $order_by, $order );
		}
	}

	/**
	 * Get the total count of items.
	 *
	 * @param int $limit Limit count.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return int
	 */
	private function get_total( $limit ) {
		return 10000;
	}

	/**
	 * Get the list of logs based on filter.
	 *
	 * @param int    $limit    Limit count.
	 * @param string $order_by Order by field.
	 * @param string $order    Order.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function get_logs( $limit, $order_by, $order = 'asc' ) {
		$logs = array(
			'limit'    => $limit,
			'order_by' => $order_by,
			'order'    => $order,
		);

		if ( $logs ) {
			$this->maybe_as_table( $logs );
		} else {
			$this->error( __( 'No logs found.', '404-to-301' ) );
		}
	}

	/**
	 * Clear the logs based on filter.
	 *
	 * @param int    $limit    Limit count.
	 * @param string $order_by Order by field.
	 * @param string $order    Order.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function clean_logs( $limit, $order_by, $order = 'asc' ) {
		$logs = array(
			'limit'    => $limit,
			'order_by' => $order_by,
			'order'    => $order,
		);

		// Get the total no. of logs.
		$total = $this->get_total( $limit );

		if ( $total > 100 ) {
			$this->clean_logs_batch( $total, $limit, $order_by, $order );
		}

		if ( $logs ) {
			// translators: %d No. of logs cleared.
			$this->success( sprintf( __( 'Succesfully cleared %d logs.', '404-to-301' ), $limit ) );
		} else {
			$this->error( __( 'No logs found.', '404-to-301' ) );
		}
	}

	/**
	 * Clear the logs based on filter by batch.
	 *
	 * @param int    $total    Total logs available.
	 * @param int    $limit    Limit count.
	 * @param string $order_by Order by field.
	 * @param string $order    Order.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function clean_logs_batch( $total, $limit, $order_by, $order = 'asc' ) {
		$logs = array(
			'limit'    => $limit,
			'order_by' => $order_by,
			'order'    => $order,
		);

		$progress = $this->make_progress( 'Clearing logs', $total );

		for ( $i = 0; $i < $total; $i ++ ) {
			sleep( 1 );
			$progress->tick();
		}

		// Finished.
		$progress->finish();

		// translators: %d No. of logs cleared.
		$this->success( sprintf( __( 'Succesfully cleared %d logs.', '404-to-301' ), $total ) );
	}
}
