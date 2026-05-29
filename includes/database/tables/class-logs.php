<?php
/**
 * The 404 logs table.
 *
 * BerlinDB's `Table` class handles `CREATE TABLE` / `dbDelta` / schema
 * versioning. We just declare the table name, version and per-version
 * upgrade routines.
 *
 * @package FourNotFour
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
	 * Schema version. Bumped only when the schema itself changes —
	 * adding a new upgrade routine and incrementing this triggers
	 * `dbDelta` on every site automatically.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $version = '4.1.0';

	/**
	 * Per-version upgrade routines.
	 *
	 * Each entry is `version => method`. Adding a new step is purely
	 * additive: declare the method, append the row, bump `$version`.
	 *
	 * @since 4.0.0
	 * @var array<string, string>
	 */
	protected $upgrades = array(
		'4.1.0' => '__4_1_0',
	);

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
			override_log      TINYINT(3) UNSIGNED NOT NULL  DEFAULT 0,
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

	/**
	 * 4.1.0 upgrade: add the per-row override columns and the new
	 * "custom redirect" status code (3).
	 *
	 * Idempotent — checks `information_schema` before each `ALTER` so
	 * re-runs (or sites that ship with a freshly-created v4.1.0 table)
	 * don't fail with "duplicate column".
	 *
	 * @since 4.1.0
	 *
	 * @return bool
	 */
	protected function __4_1_0(): bool { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore, PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- BerlinDB looks up schema upgrade callbacks by the `__<version>` naming convention; we don't get to pick a different prefix.
		$db = $this->get_db();

		if ( empty( $db ) ) {
			return false;
		}

		$columns = array(
			'override_redirect' => "ALTER TABLE {$this->table_name} ADD COLUMN override_redirect TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER status",
			'override_log'      => "ALTER TABLE {$this->table_name} ADD COLUMN override_log TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER override_redirect",
			'override_email'    => "ALTER TABLE {$this->table_name} ADD COLUMN override_email TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER override_log",
		);

		foreach ( $columns as $column => $sql ) {
			$exists = $db->get_var(
				$db->prepare(
					'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s',
					DB_NAME,
					$this->table_name,
					$column
				)
			);

			if ( $exists ) {
				continue;
			}

			$db->query( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		}

		return true;
	}
}
