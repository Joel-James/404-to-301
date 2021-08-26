<?php
/**
 * The redirects table class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Tables
 * @subpackage Redirects
 */

namespace DuckDev\Redirect\Database\Tables;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Table;

/**
 * Class Redirects.
 *
 * @since   4.0.0
 * @extends Table
 * @package DuckDev\Redirect\Database\Tables
 */
final class Redirects extends Table {

	/**
	 * Table name, without the global table prefix.
	 *
	 * @var    string
	 * @access public
	 * @since  4.0.0
	 */
	public $name = 'redirects';

	/**
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @var    string
	 * @access protected
	 * @since  4.0.0
	 */
	protected $prefix = '404_to_301';

	/**
	 * Optional description for table.
	 *
	 * @var    string
	 * @access public
	 * @since  4.0.0
	 */
	public $description = 'Redirects';

	/**
	 * Current database table version.
	 *
	 * @var    mixed
	 * @access protected
	 * @since  4.0.0
	 */
	protected $version = '1.0.0';

	/**
	 * Database version key (saved in _options or _sitemeta).
	 *
	 * @var    string
	 * @access protected
	 * @since  4.0.0
	 */
	protected $db_version_key = '404_to_301_redirects_version';

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
			source mediumtext NOT NULL,
			destination mediumtext NOT NULL,
			code int(11) unsigned DEFAULT '301',
			type enum('url') DEFAULT 'url',
			status enum('enabled', 'disabled', 'ignored') DEFAULT 'enabled',
			meta mediumtext DEFAULT NULL,
			created_at datetime NOT NULL default CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT NULL,
			created_by bigint(20) unsigned DEFAULT NULL,
			updated_by bigint(20) unsigned DEFAULT NULL,
			PRIMARY KEY (id)
			";
	}
}
