<?php
/**
 * The queue process base class for our plugin.
 *
 * All background process should extend this class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Abstracts
 * @subpackage Process
 */

namespace DuckDev\Redirect\Utils;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Queue\Task;

/**
 * Class Process
 *
 * @since   4.0.0
 * @extends Task
 * @package DuckDev\Redirect\Utils
 */
abstract class Process extends Task {

	/**
	 * Our custom process prefix.
	 *
	 * @var    string $prefix
	 *
	 * @since  4.0.0
	 * @access protected
	 */
	protected $prefix = '404_to_301';

	/**
	 * A unique action name.
	 *
	 * All extending classes should use a unique name to
	 * avoid conflicts with multiple processes.
	 *
	 * @var string $action
	 *
	 * @access protected
	 * @since  1.0.0
	 */
	protected $action = 'queue_process';

	/**
	 * Instance obtaining method.
	 *
	 * @since 4.0.0
	 *
	 * @return static Called class instance.
	 */
	public static function get() {
		static $instances = array();

		// @codingStandardsIgnoreLine Plugin-backported
		$called_class_name = get_called_class();

		// Only if not already exist.
		if ( ! isset( $instances[ $called_class_name ] ) ) {
			$instances[ $called_class_name ] = new $called_class_name();

			// Optionally initialize the class.
			if ( method_exists( $instances[ $called_class_name ], 'init' ) ) {
				$instances[ $called_class_name ]->init();
			}
		}

		return $instances[ $called_class_name ];
	}

	/**
	 * Get the unique identifier of current process.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->cron_hook_identifier;
	}

	/**
	 * Check if current process is running.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_running() {
		return $this->is_process_running();
	}

	/**
	 * Current queued process is completed.
	 *
	 * Don't forget to call parent:complete() when you
	 * override this in extending class.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function complete() {
		// Mark as completed.
		parent::complete();

		/**
		 * Action hook to run after all queued tasks are completed.
		 *
		 * @param string $action Current process' action name.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_process_completed', $this->action );
	}
}
