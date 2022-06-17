<?php
/**
 * The log table class.
 *
 * We are not creating a separate meta table for logs because that
 * can affect the db size if there are 1000s of 404s.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Database\Tables
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Database\Tables;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Table;

/**
 * Class Logs.
 *
 * @since   4.0.0
 * @extends Table
 * @package DuckDev\Redirect\Database\Tables
 */
final class Logs extends Table {

	/**
	 * Table name, without the global table prefix.
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access public
	 */
	public $name = 'logs';

	/**
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access protected
	 */
	protected $prefix = '404_to_301';

	/**
	 * Optional description for table.
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access public
	 */
	public $description = 'Logs';

	/**
	 * Current database table version.
	 *
	 * @since  4.0.0
	 * @var    mixed
	 * @access protected
	 */
	protected $version = '1.0.0';

	/**
	 * Database version key (saved in _options or _sitemeta).
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access protected
	 */
	protected $db_version_key = '404_to_301_logs_version';

	/**
	 * Key => value array of versions => methods.
	 *
	 * @since  4.0.0
	 * @var    array
	 * @access protected
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
			log_id bigint(20) unsigned NOT NULL auto_increment,
			url mediumtext NOT NULL,
			referrer varchar(255) DEFAULT NULL,
			ip varchar(45) DEFAULT NULL,
			agent varchar(255) DEFAULT NULL,
			request_method varchar(10) DEFAULT 'GET',
			request_data mediumtext DEFAULT NULL,
			meta mediumtext DEFAULT NULL,
			visits bigint(20) unsigned DEFAULT '1',
			redirect_status enum('global', 'enabled', 'disabled') DEFAULT 'global',
			log_status enum('global', 'enabled', 'disabled') DEFAULT 'global',
			email_status enum('global', 'enabled', 'disabled') DEFAULT 'global',
			redirect_id bigint(20) unsigned DEFAULT NULL,
			created_at datetime NOT NULL default CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT NULL,
			updated_by bigint(20) unsigned DEFAULT NULL,
			PRIMARY KEY (log_id)
			";
	}
}
