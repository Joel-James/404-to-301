<?php
/**
 * The custom redirects table.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database\Tables;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Table;

/**
 * Class Redirects
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Database\Tables
 */
final class Redirects extends Table {

	/**
	 * Table name (without the WP table prefix).
	 *
	 * The full name is `{$wpdb->prefix}404_to_301_redirects`.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $name = '404_to_301_redirects';

	/**
	 * Schema version. Bumped only when the schema itself changes.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $version = '4.1.0';

	/**
	 * Per-version upgrade routines.
	 *
	 * Bumping `$version` is enough for plain column additions — dbDelta
	 * runs from the schema string above and adds missing columns
	 * automatically. We only need explicit `__<version>` callbacks
	 * here when an alteration can't be expressed in the base schema
	 * (data backfills, index renames, type changes, etc.).
	 *
	 * @since 4.0.0
	 * @var array<string, string>
	 */
	protected $upgrades = array();

	/**
	 * Wire the schema in.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function set_schema(): void {
		$this->schema = "
			id              BIGINT(20)    UNSIGNED  NOT NULL  AUTO_INCREMENT,
			source          VARCHAR(2048)           NOT NULL,
			source_hash     CHAR(40)                NOT NULL,
			match_type      VARCHAR(10)             NOT NULL  DEFAULT 'exact',
			target_type     VARCHAR(10)             NOT NULL  DEFAULT 'link',
			target_url      VARCHAR(2048)           NOT NULL  DEFAULT '',
			target_page_id  BIGINT(20)    UNSIGNED            DEFAULT NULL,
			redirect_type   SMALLINT(5)   UNSIGNED  NOT NULL  DEFAULT 301,
			is_active       TINYINT(3)    UNSIGNED  NOT NULL  DEFAULT 1,
			hits            INT(11)       UNSIGNED  NOT NULL  DEFAULT 0,
			last_hit_at     DATETIME                          DEFAULT NULL,
			notes           TEXT                              DEFAULT NULL,
			modified_by     BIGINT(20)    UNSIGNED            DEFAULT NULL,
			query_handling  VARCHAR(10)             NOT NULL  DEFAULT 'ignore',
			created_at      DATETIME                NOT NULL  DEFAULT '0000-00-00 00:00:00',
			updated_at      DATETIME                NOT NULL  DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			UNIQUE KEY source_hash (source_hash),
			KEY is_active (is_active),
			KEY redirect_type (redirect_type),
			KEY match_type (match_type),
			KEY modified_by (modified_by)
		";
	}
}
