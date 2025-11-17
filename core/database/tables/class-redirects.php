<?php
/**
 * The redirects table class.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Database\Tables
 * @subpackage Redirects
 */

namespace DuckDev\FourNotFour\Database\Tables;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Table;

/**
 * Class Redirects.
 *
 * @since   4.0.0
 * @extends Table
 * @package DuckDev\FourNotFour\Database\Tables
 */
final class Redirects extends Table {

	/**
	 * Table name, without the global table prefix.
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access public
	 */
	public $name = 'redirects';

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
	public $description = 'Redirects';

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
	protected $db_version_key = '404_to_301_redirects_version';

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
		$this->schema = "
			redirect_id bigint(20) unsigned NOT NULL auto_increment,
			source mediumtext NOT NULL,
			destination mediumtext NOT NULL,
			type int(11) unsigned DEFAULT '301',
			`group` enum('custom', '404') DEFAULT 'custom',
			`status` enum('enabled', 'disabled') DEFAULT 'enabled',
			meta mediumtext DEFAULT NULL,
			hash varchar(50) NOT NULL UNIQUE,
			created_at datetime NOT NULL default CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT NULL,
			created_by bigint(20) unsigned DEFAULT NULL,
			updated_by bigint(20) unsigned DEFAULT NULL,
			PRIMARY KEY (redirect_id)
			";
	}
}
