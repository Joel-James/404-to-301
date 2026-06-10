<?php
/**
 * The 404 logs table.
 *
 * BerlinDB's `Table` class handles `CREATE TABLE` / `dbDelta` / schema
 * versioning. We just declare the table name, version and per-version
 * upgrade routines.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database\Tables;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Table;

/**
 * Class Logs
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Database\Tables
 */
final class Logs extends Table {

	/**
	 * Table name (without the WP table prefix).
	 *
	 * The full name is `{$wpdb->prefix}404_to_301_logs`.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $name = '404_to_301_logs';

	/**
	 * Schema version.
	 *
	 * 4.0.0 is the first BerlinDB-backed schema; v3 → v4 migration is
	 * handled separately by {@see \DuckDev\FourNotFour\Migration\Migrator},
	 * not as a BerlinDB upgrade step. Don't bump this for changes made
	 * before v4 ships — fold the change into the base schema string
	 * instead so every install gets it on the first `dbDelta` pass.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $version = '4.0.0';

	/**
	 * Wire the schema in.
	 *
	 * BerlinDB calls this from its constructor; the assignment makes
	 * the columns available to `create()` (the dbDelta path) and to
	 * the matching {@see \DuckDev\FourNotFour\Database\Queries\Log}.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function set_schema(): void {
		$this->schema = "
			id            BIGINT(20)    UNSIGNED  NOT NULL  AUTO_INCREMENT,
			url           VARCHAR(2048)           NOT NULL,
			url_hash      CHAR(40)                NOT NULL,
			ref           VARCHAR(2048)           NOT NULL  DEFAULT '',
			ip            VARBINARY(16)           NOT NULL  DEFAULT '',
			ua            VARCHAR(512)            NOT NULL  DEFAULT '',
			method        VARCHAR(10)             NOT NULL  DEFAULT 'GET',
			hits          INT(11)       UNSIGNED  NOT NULL  DEFAULT 1,
			redirect_id   BIGINT(20)    UNSIGNED            DEFAULT NULL,
			status        TINYINT(3)    UNSIGNED  NOT NULL  DEFAULT 0,
			override_redirect TINYINT(3) UNSIGNED NOT NULL  DEFAULT 0,
			override_email    TINYINT(3) UNSIGNED NOT NULL  DEFAULT 0,
			created_at    DATETIME                NOT NULL  DEFAULT '0000-00-00 00:00:00',
			updated_at    DATETIME                NOT NULL  DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			UNIQUE KEY url_hash (url_hash),
			KEY status (status),
			KEY created_at (created_at),
			KEY redirect_id (redirect_id)
		";
	}
}
