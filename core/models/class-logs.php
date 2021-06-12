<?php
/**
 * The error logs model class.
 *
 * This class handles the database queries for error logs
 * management.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @since      4.0.0
 * @subpackage Settings
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Model;

/**
 * Class Tables.
 *
 * @package DuckDev\DD4T3\Models
 */
class Logs extends Model {

	/**
	 * Setup the plugin and register all hooks.
	 *
	 * Pro version features and not initialized yet, so do not
	 * execute something on this hooks if you are checking for
	 * Pro version.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public function get_list() {
		$list = array();

		/**
		 * Action hook to trigger after initializing all core actions.
		 *
		 * You still need to check if it Pro version or Free.
		 *
		 * @since 1.8.0
		 */
		return apply_filters( '404_to_301_model_logs_get_list', $list );
	}

	/**
	 * Get the table creation query for the main log table.
	 *
	 * This query is going to be used with dbDelta function
	 * to upgrade the table structure or create new table.
	 *
	 * @see   https://codex.wordpress.org/Creating_Tables_with_Plugins
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function logs_table_schema() {
		// Get table name.
		$table = $this->get_table_name( self::LOG_TABLE );

		$query = "CREATE TABLE $table (
            id BIGINT NOT NULL AUTO_INCREMENT,
            date DATETIME NOT NULL,
            url VARCHAR(512) NOT NULL,
            ref VARCHAR(512) NOT NULL default '',
            ip VARCHAR(40) NOT NULL default '',
            ua VARCHAR(512) NOT NULL default '',
            redirect VARCHAR(512) NULL default '',
			options LONGTEXT,
			status BIGINT NOT NULL default 1,
            PRIMARY KEY  (id)
        );";

		/**
		 * Filter hook to modify the logs table schema.
		 *
		 * @param string $query Query string.
		 *
		 * @since 1.8.0
		 */
		return apply_filters( '404_to_301_log_table_schema', $query );
	}
}
