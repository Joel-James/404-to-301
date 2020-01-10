<?php

namespace DuckDev\WP404\Utils\Abstracts;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\EloquentWP\Eloquent\Model as Model_Base;

/**
 * Singleton class for all classes.
 *
 * @link   https://duckdev.com
 * @since  3.2.0
 *
 * @author Joel James <me@joelsays.com>
 */
abstract class Model extends Model_Base {

	/**
	 * Disable created_at and update_at columns.
	 *
	 * @var bool
	 *
	 * @since 4.0.0
	 */
	public $timestamps = false;

	/**
	 * Set primary key as ID, because WordPress
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * Make ID guarded -- without this ID doesn't save.
	 *
	 * @var string
	 */
	protected $guarded = [ 'id' ];

	/**
	 * Overide parent method to make sure prefixing is correct.
	 *
	 * @return string
	 */
	public function getTable() {
		//In this example, it's set, but this is better in an abstract class
		if ( isset( $this->table ) ) {
			$prefix = $this->getConnection()->db->prefix;
			return $prefix . $this->table;

		}

		return parent::getTable();
	}
}
