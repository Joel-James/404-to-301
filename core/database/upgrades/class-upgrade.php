<?php
/**
 * The base class to extend for upgrade processes.
 *
 * This class should be extended by all the upgrade processes
 * then only upgrade class will be able to recognize it.
 *
 * @since      1.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/
 * @package    Upgrades
 * @subpackage Upgrader
 */

namespace DuckDev\Redirect\Database\Upgrades;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Base;

/**
 * Class Upgrade.
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Upgrades
 */
abstract class Upgrade extends Base {

	/**
	 * Holds a unique name for the process.
	 *
	 * IMPORTANT: This should be set in extending classes to a unique
	 * id or else the upgrader will break.
	 *
	 * @var string $id
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected $id = 'upgrade_process';

	/**
	 * Check if we can continue with upgrade.
	 *
	 * Override this to add additional checks before
	 * starting upgrade. Return false to deny upgrade.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function should_upgrade() {
		return true;
	}

	/**
	 * Override to do something on upgrade complete.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade_complete() {}

	/**
	 * Get the id of current process.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * @param string $table  Table key.
	 * @param bool   $prefix Should prefix?.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_table_name( $table, $prefix = true ) {
		if ( $prefix ) {
			global $wpdb;

			$table = $wpdb->prefix . $table;
		}

		return $table;
	}

	/**
	 * Get an item from data array.
	 *
	 * If not found, return default value.
	 *
	 * @param string $key     Item key.
	 * @param array  $data    Data to check.
	 * @param mixed  $default Default value.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return mixed
	 */
	protected function get_value( $key, $data, $default = '' ) {
		return isset( $data[ $key ] ) ? $data[ $key ] : $default;
	}

	/**
	 * Run MySQL query using wpdb.
	 *
	 * @param string $query Query.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	protected function do_query( $query ) {
		global $wpdb;

		return $wpdb->query( $query ); // phpcs:ignore
	}

	/**
	 * Get next set of data to upgrade.
	 *
	 * If the data is large, return a small set of
	 * data and keep the rest. Until you return an
	 * empty array or false, we will come back to this
	 * method to get next set of data after completing
	 * one set.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array|false
	 */
	abstract public function get_data();

	/**
	 * Process single item from the upgrade queue.
	 *
	 * @param mixed $item Item to process.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array|false
	 */
	abstract public function upgrade_task( $item );
}
