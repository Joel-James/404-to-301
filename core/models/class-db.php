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

use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class Tables.
 *
 * @package DuckDev\DD4T3\Models
 */
class DB extends Base {

	/**
	 * Current table name.
	 *
	 * @var string $table Table name.
	 *
	 * @since 4.0
	 */
	private $tables = array(
		'logs'    => '404_to_301',
		'options' => '404_to_301_options',
	);

	/**
	 * Field names and formats of the table.
	 *
	 * @var string[] $fields
	 *
	 * @since 4.0
	 */
	private $fields = array(
		'logs'    => array(
			'id'         => '%d',
			'date'       => '%s',
			'url'        => '%s',
			'ref'        => '%s',
			'ip'         => '%s',
			'ua'         => '%s',
			'redirect'   => '%s', // @deprecated.
			'updated_at' => '%s',
			'updated_by' => '%d',
			'status'     => '%d',
		),
		'options' => array(
			'id'              => '%d',
			'log_id'          => '%d',
			'redirect'        => '%d',
			'log'             => '%d',
			'email'           => '%d',
			'redirect_target' => '%s',
			'redirect_type'   => '%d',
			'created_by'      => '%d',
			'updated_by'      => '%d',
			'created_at'      => '%s',
			'updated_at'      => '%s',
			'status'          => '%d',
		),
	);

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
	protected function schemas() {
		$schemas = array(
			'logs'    => "CREATE TABLE {$this->table_name('logs')} (
	            id BIGINT NOT NULL AUTO_INCREMENT,
	            date DATETIME NOT NULL,
	            url VARCHAR(512) NOT NULL,
	            ref VARCHAR(512) NOT NULL default '',
	            ip VARCHAR(40) NOT NULL default '',
	            ua VARCHAR(512) NOT NULL default '',
	            updated_at DATETIME NOT NULL,
				updated_by INT NOT NULL default 0,
				status INT NOT NULL default 1,
	            PRIMARY KEY  (id)
	        );",
			'options' => "CREATE TABLE {$this->table_name('options')} (
	            id BIGINT NOT NULL AUTO_INCREMENT,
	            log_id BIGINT NOT NULL,
	            redirect BIGINT NOT NULL default 0,
	            log BIGINT NOT NULL default 0,
	            email BIGINT NOT NULL default 0,
	            redirect_target VARCHAR(512) NOT NULL default '',
	            redirect_type INT NOT NULL default 301,
	            created_by INT NOT NULL default 0,
				updated_by INT NOT NULL default 0,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				status BIGINT NOT NULL default 1,
	            PRIMARY KEY  (id)
	        );",
		);

		return apply_filters( 'dd404_db_schemas', $schemas );
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * Classes can override this by extending it.
	 *
	 * @param string $table Table key.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function table_name( $table ) {
		if ( isset( $this->tables[ $table ] ) ) {
			global $wpdb;

			return $wpdb->prefix . $this->table;
		}

		return false;
	}

	/**
	 * Get the field format string.
	 *
	 * @param string $table Table key.
	 *
	 * @since 4.0
	 *
	 * @return string[]
	 */
	public function field_formats( $table ) {
		if ( isset( $this->tables[ $table ] ) ) {
			return array_values( $this->fields[ $table ] );
		}

		return array();
	}

	/**
	 * Get the field names of the table.
	 *
	 * @param string $table Table key.
	 *
	 * @since 4.0
	 *
	 * @return string[]
	 */
	public function field_names( $table ) {
		if ( isset( $this->tables[ $table ] ) ) {
			return array_keys( $this->fields[ $table ] );
		}

		return array();
	}

	/**
	 * Check the table status in DB.
	 *
	 * This is used to check if the current table
	 * is created.
	 *
	 * @param string $table Table key.
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	public function table_ready( $table ) {
		global $wpdb;

		// phpcs:ignore
		$found = $wpdb->get_var(
			$wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$this->table_name( $table )
			)
		);

		return $found === $this->table_name( $table );
	}

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
		// Get the create schemas.
		$tables = array(
			Models\Logs::instance(),
			Models\Options::instance(),
		);

		// Make sure dbDelta is available to handle DB upgrades properly.
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$done = array();

		foreach ( $tables as $table ) {
			if ( ! empty( $table->schema() ) ) {
				// Update or create table in database.
				$result = dbDelta( $table->schema() );

				if ( ! empty( $result ) ) {
					if ( $table->table_ready() ) {
						$done[] = $table->table_name();
					}
				}
			}
		}

		if ( ! empty( $done ) ) {
			$ready = Settings::instance()->get( 'tables', 'misc', array() );
			$ready = array_merge( $done, $ready );
			// Mark the done tables as ready.
			Settings::instance()->update(
				'tables',
				array_unique( $ready ),
				'misc'
			);
		}

		/**
		 * Action hook to trigger after creating tables.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_db_after_table_create' );
	}
}
