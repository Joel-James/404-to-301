<?php
/**
 * The queue process class for our plugin.
 *
 * All background process should be registered using
 * this class `dd4t3_queue_processes` filter.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Process
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Base;
use DuckDev\Redirect\Utils\Abstracts\Process;

/**
 * Class Processes
 *
 * @since   4.0.0
 * @package DuckDev\Redirect
 * @extends Base
 */
class Processes extends Base {

	/**
	 * Array of initialized processed.
	 *
	 * @var Process[] $processes
	 *
	 * @since  4.0.0
	 * @access private
	 */
	private $processes = array();

	/**
	 * Initialize the processed unconditionally.
	 *
	 * All registered process should be initialized
	 * unconditionally. It's a requirement by the process
	 * library.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	protected function init() {
		foreach ( $this->processes() as $process => $class ) {
			if (
				! isset( $this->processes[ $process ] ) &&
				is_subclass_of( $class, '\DuckDev\Redirect\Utils\Abstracts\Process' )
			) {
				// Initialize the process class.
				$this->processes[ $process ] = new $class();

				/**
				 * Action hook to run after a queue process is initialized.
				 *
				 * @param string  $process  Process name.
				 * @param string  $class    Process class name.
				 * @param Process $instance Initialized class object.
				 *
				 * @since 4.0.0
				 */
				do_action( 'dd4t3_process_init', $process, $class, $this->processes[ $process ] );
			}
		}
	}

	/**
	 * Check if a process is available.
	 *
	 * Process will be available only if it's initialized.
	 *
	 * @param string $name Process name.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function has_process( $name ) {
		return isset( $this->processes[ $name ] );
	}

	/**
	 * Get a single process instance.
	 *
	 * @param string $name Process name.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return Process|false
	 */
	public function get_process( $name ) {
		if ( $this->has_process( $name ) ) {
			return $this->processes[ $name ];
		}

		return false;
	}

	/**
	 * Get the list of initialized processes.
	 *
	 * Not all registered process will be available if
	 * we were not able to initialize it.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return Process[]
	 */
	public function get_processes() {
		return $this->processes;
	}

	/**
	 * Check if process running.
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 *
	 * @param string $name Process name.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	public function is_running( $name ) {
		if ( $this->get_process( $name ) ) {
			// Setup transient key.
			$key = $this->get_process( $name )->get_id() . '_process_lock';
			// Process already running if transient is set.
			if ( get_site_transient( $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the list of registered processes.
	 *
	 * Each process should extend the Process class.
	 * Otherwise, it won't be initialized.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function processes() {
		$processes = array();

		/**
		 * Filter to add new processes to plugin processes.
		 *
		 * @param Process[] $processes Process classes.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_queue_processes', $processes );
	}
}
