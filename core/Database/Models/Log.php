<?php
/**
 * Represents release objects.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace DuckDev\WP404\Database\Models;

use IronBound\Cache\Cache;
use IronBound\DB\Model;
use IronBound\DB\Table\Table;
use IronBound\DB\Manager;
use IronBound\DB\Exception as DB_Exception;

/**
 * Class Release
 *
 * @package ITELIC
 */
class Log extends Model {

	/**
	 * Create a new release record.
	 *
	 * If status is set to active, the start date will automatically be set to
	 * now.
	 *
	 * @param string $url
	 * @param array  $data
	 *
	 * @since 1.0
	 *
	 * @throws DB_Exception
	 * @return null
	 */
	public static function create( $url, $data = [] ) {
		$data = array(
			'url'      => $url,
			'ref'      => $data['ref'],
			'ip'       => $data['ip'],
			'ua'       => $data['ua'],
			'redirect' => $data['redirect'],
			'options'  => $data['options'],
			'status'   => $data['status'],
		);
		$db   = Manager::make_simple_query_object( '404_to_301' );
		$id   = $db->insert( $data );
		$log  = self::get( $id );
		if ( $log ) {
			Cache::add( $log );
		}
		return $log;
	}

	/**
	 * Get the unique pk for this record.
	 *
	 * @since 1.0
	 *
	 * @return mixed (generally int, but not necessarily).
	 */
	public function get_pk() {
		return $this->id;
	}

	/**
	 * Retrieve the ID of this release.
	 *
	 * @since 1.0
	 *
	 * @return int
	 */
	public function get_ID() {
		return $this->get_pk();
	}

	/**
	 * Retrieve the ID of this release.
	 *
	 * @since 1.0
	 *
	 * @return int
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Get the status of this Release.
	 *
	 * @param bool $label
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set the status of this release.
	 *
	 * @param string $status
	 *
	 * @since 1.0
	 *
	 */
	public function set_status( $status ) {
		$this->status = (int) $status;
	}

	/**
	 * Get the table object for this model.
	 *
	 * @since 1.0
	 *
	 * @returns Table
	 */
	protected static function get_table() {
		return Manager::get( '404_to_301' );
	}
}