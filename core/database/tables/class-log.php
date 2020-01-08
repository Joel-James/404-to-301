<?php
/**
 * DB table managing releases.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace DuckDev\WP404\Database\Tables;

use DuckDev\WP404\Database\Tables\Column\Array_Column;
use IronBound\DB\Table\Column;
use IronBound\DB\Table\Table;

/**
 * Class Releases
 *
 * @package ITELIC\DB\Table
 */
class Log implements Table {

	const TABLE = '404_to_301';

	/**
	 * Retrieve the name of the database table.
	 *
	 * @param \wpdb $wpdb
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_table_name( \wpdb $wpdb ) {
		return $wpdb->prefix . '404_to_301';
	}

	/**
	 * Get the slug of this table.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return '404_to_301';
	}

	/**
	 * Columns in the table.
	 *
	 * key => sprintf field type
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'id'       => new Column\IntegerBased( 'BIGINT', 'id' ),
			'date'     => new Column\DateTime( 'date' ),
			'url'      => new Column\StringBased( 'VARCHAR', 'url' ),
			'ref'      => new Column\StringBased( 'VARCHAR', 'ref' ),
			'ip'       => new Column\StringBased( 'VARCHAR', 'ip' ),
			'ua'       => new Column\StringBased( 'VARCHAR', 'ua' ),
			'redirect' => new Column\StringBased( 'VARCHAR', 'redirect' ),
			'options'  => new Array_Column( 'LONGTEXT', 'options' ),
			'status'   => new Column\Boolean( 'status' ),
		);
	}

	/**
	 * Default column values.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'id'       => '',
			'date'     => '',
			'url'      => '',
			'ref'      => '',
			'ip'       => '',
			'ua'       => '',
			'redirect' => '',
			'options'  => [],
			'status'   => 1,
		);
	}

	/**
	 * Retrieve the name of the primary key.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_primary_key() {
		return 'id';
	}

	/**
	 * Get creation SQL.
	 *
	 * @param \wpdb $wpdb
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_creation_sql( \wpdb $wpdb ) {
		$tn = $this->get_table_name( $wpdb );
		return "CREATE TABLE {$tn} (
		id BIGINT NOT NULL AUTO_INCREMENT,
        date DATETIME NOT NULL,
        url VARCHAR(512) NOT NULL,
        ref VARCHAR(512) NOT NULL default '',
        ip VARCHAR(40) NOT NULL default '',
        ua VARCHAR(512) NOT NULL default '',
        redirect VARCHAR(512) NULL default '',
		options LONGTEXT,
		status BIGINT NOT NULL default 1,
		PRIMARY KEY  (id),
		KEY product__version (product,version)
		);";
	}

	/**
	 * Retrieve the version number of the current table schema as written.
	 *
	 * The version should be incremented by 1 for each change.
	 *
	 * @since 1.0
	 *
	 * @return int
	 */
	public function get_version() {
		return 4;
	}
}