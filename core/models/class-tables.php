<?php
/**
 * The core plugin class.
 *
 * @link    http://premium.wpmudev.org
 * @since   1.8.0
 *
 * @author  Joel James <joel@incsub.com>
 * @package DuckDev\Redirect
 */

namespace DDuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Abstracts\Model;

/**
 * Class Tables.
 *
 * @package DuckDev\DD4T3\Models
 */
class Tables extends Model {

	/**
	 * Main log table name.
	 *
	 * @since 4.0.0
	 */
	const LOG_TABLE = '404_to_301';

	/**
	 * Custom options table for the logs.
	 *
	 * @since 4.0.0
	 */
	const OPTIONS_TABLE = '404_to_301_options';

	/**
	 * Setup the plugin and register all hooks.
	 *
	 * Pro version features and not initialized yet, so do not
	 * execute something on this hooks if you are checking for
	 * Pro version.
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public function create() {
		/**
		 * Action hook to trigger after initializing all core actions.
		 *
		 * You still need to check if it Pro version or Free.
		 *
		 * @since 1.8.0
		 */
		do_action( 'wpmudev_vids_after_core_init' );
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

	/**
	 * Get the table creation query for the log options table.
	 *
	 * This query is going to be used with dbDelta function
	 * to upgrade the table structure or create new table.
	 *
	 * @see   https://codex.wordpress.org/Creating_Tables_with_Plugins
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function options_table_schema() {
		// Get table name.
		$table = $this->get_table_name( self::OPTIONS_TABLE );

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
		 * Filter hook to modify the logs options table schema.
		 *
		 * @param string $query Query string.
		 *
		 * @since 1.8.0
		 */
		return apply_filters( '404_to_301_log_options_table_schema', $query );
	}
}
