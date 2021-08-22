<?php
/**
 * The options table class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Tables
 * @subpackage Options
 */

namespace DuckDev\Redirect\Database\Tables;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Table;

/**
 * Class Options.
 *
 * @since   4.0.0
 * @extends Table
 * @package DuckDev\Redirect\Database\Tables
 */
class Options extends Table {

	/**
	 * Table name, without the global table prefix.
	 *
	 * @var    string
	 * @access public
	 * @since  4.0.0
	 */
	public $name = '404_to_301_options';

	/**
	 * Database version key (saved in _options or _sitemeta)
	 *
	 * @var    string
	 * @access protected
	 * @since  4.0.0
	 */
	protected $db_version_key = '404_to_301_options_version';

	/**
	 * Optional description for table.
	 *
	 * @var    string
	 * @access public
	 * @since  4.0.0
	 */
	public $description = 'Options';

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
			log_id bigint(20) unsigned DEFAULT NULL,
			redirect_id bigint(20) unsigned DEFAULT NULL,
			redirect_status enum('global', 'enabled', 'disabled') DEFAULT 'global',
			log_status enum('global', 'enabled', 'disabled') DEFAULT 'global',
			email_status enum('global', 'enabled', 'disabled') DEFAULT 'global',
			created_at datetime NOT NULL default CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT NULL,
			updated_by bigint(20) unsigned DEFAULT NULL,
			CONSTRAINT options_unique_ids UNIQUE (log_id, redirect_id),
			PRIMARY KEY (id)
			";
	}
}
