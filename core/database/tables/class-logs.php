<?php
/**
 * The log table class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Tables
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Database\Tables;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Table;

/**
 * Class Table.
 *
 * @since   4.0.0
 * @extends Table
 * @package DuckDev\Redirect\Database\Tables
 */
class Logs extends Table {

	/**
	 * Table name, without the global table prefix.
	 *
	 * @var    string
	 * @access public
	 * @since  4.0.0
	 */
	public $name = '404_to_301_logs';

	/**
	 * Database version key (saved in _options or _sitemeta)
	 *
	 * @var    string
	 * @access protected
	 * @since  4.0.0
	 */
	protected $db_version_key = '404_to_301_logs_version';

	/**
	 * Optional description for table.
	 *
	 * @var    string
	 * @access public
	 * @since  4.0.0
	 */
	public $description = 'Logs';

	/**
	 * Current database table version.
	 *
	 * @var    mixed
	 * @access protected
	 * @since  4.0.0
	 */
	protected $version = '1.0.0';

	/**
	 * Key => value array of versions => methods.
	 *
	 * @var    array
	 * @access protected
	 * @since  4.0.0
	 */
	protected $upgrades = array();

	/**
	 * Setup this database table.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function set_schema() {
		// phpcs:ignore
		$this->schema = "
			id bigint(20) unsigned NOT NULL auto_increment,
			url mediumtext NOT NULL,
			referrer varchar(255) DEFAULT NULL,
			ip varchar(45) DEFAULT NULL,
			agent varchar(255) DEFAULT NULL,
			method varchar(10) DEFAULT NULL,
			request mediumtext DEFAULT NULL,
			created_at datetime NOT NULL default CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
			";
	}
}
