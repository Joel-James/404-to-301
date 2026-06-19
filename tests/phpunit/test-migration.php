<?php
/**
 * Tests for the v3 → v4 {@see \DuckDev\FourNotFour\Migration\Migrator}.
 *
 * Each test seeds the legacy `wp_404_to_301` table with hand-rolled
 * SQL, exercises a migrator entry point, and asserts the resulting
 * shape of the v4 tables and the migration flags.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Migration\Migrator;
use DuckDev\FourNotFour\Models\Logs as LogsModel;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;
use DuckDev\FourNotFour\Settings;

/**
 * Class MigrationTest
 *
 * @group migration
 */
class MigrationTest extends WP_UnitTestCase {

	public function set_up(): void {
		parent::set_up();
		Database::instance();

		// Intentionally NOT writing `Settings::KEY` here — a write
		// inside the WP_UnitTestCase transaction would be committed by
		// the first DDL statement in this test (CREATE / DROP TABLE)
		// and then leak across into the next test class. Migrator code
		// reads via `Settings::get($key, false)`, which falls back to
		// the documented defaults when the option doesn't exist on
		// disk, so this is safe.
	}

	public function tear_down(): void {
		global $wpdb;
		// `CREATE/DROP TABLE` + `TRUNCATE` are implicit-commit DDL, so
		// they end the WP_UnitTestCase transaction mid-test. Drop the
		// legacy table and truncate the v4 tables so the next test
		// starts clean.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}404_to_301" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}404_to_301_logs" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}404_to_301_redirects" );

		parent::tear_down();

		// `delete_option` inside the transaction would be rolled back by
		// `parent::tear_down()` and silently restore whatever the
		// implicit DDL commit froze on disk. Run it AFTER the rollback
		// so the delete sticks for the next test class.
		delete_option( Settings::KEY );
		wp_cache_delete( Settings::KEY, 'options' );
	}

	/**
	 * Build the legacy v3 schema. The shape mirrors what the v3 plugin
	 * shipped: a single table holding both 404 logs and (optionally) a
	 * per-row custom redirect.
	 */
	private function create_legacy_table(): void {
		global $wpdb;

		$table = $wpdb->prefix . '404_to_301';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS {$table} (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				url TEXT NOT NULL,
				ref TEXT NULL,
				ip VARCHAR(64) NULL,
				ua TEXT NULL,
				date DATETIME NULL,
				redirect TEXT NULL,
				options LONGTEXT NULL,
				PRIMARY KEY (id)
			)"
		);
	}

	private function insert_legacy_row( array $row ): void {
		global $wpdb;

		$defaults = array(
			'url'      => '',
			'ref'      => '',
			'ip'       => '',
			'ua'       => '',
			'date'     => current_time( 'mysql', true ),
			'redirect' => null,
			'options'  => null,
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert( $wpdb->prefix . '404_to_301', array_merge( $defaults, $row ) );
	}

	/**
	 * Without the legacy `wp_404_to_301` table on disk, the
	 * detector reports false (so the activator can short-circuit).
	 */
	public function test_legacy_table_exists_false_when_absent(): void {
		$this->assertFalse( Migrator::instance()->legacy_table_exists() );
	}

	/**
	 * Once the legacy table is created, the detector sees it — that's
	 * the trigger for phase 1 / phase 2 to run.
	 */
	public function test_legacy_table_exists_true_after_creation(): void {
		$this->create_legacy_table();
		$this->assertTrue( Migrator::instance()->legacy_table_exists() );
	}

	/**
	 * Phase 1 moves every legacy row that has a non-empty redirect
	 * into the v4 redirects table, preserving the configured type
	 * when the legacy `options` blob carries one.
	 */
	public function test_phase1_migrates_custom_redirects(): void {
		$this->create_legacy_table();

		$this->insert_legacy_row(
			array(
				'url'      => '/legacy-301',
				'redirect' => 'https://example.com/new-301',
				'options'  => maybe_serialize( array( 'type' => 301 ) ),
			)
		);
		$this->insert_legacy_row(
			array(
				'url'      => '/legacy-302',
				'redirect' => 'https://example.com/new-302',
				'options'  => maybe_serialize( array( 'type' => 302 ) ),
			)
		);
		// Row with no redirect — must be ignored by phase 1.
		$this->insert_legacy_row( array( 'url' => '/just-a-404' ) );

		$inserted = Migrator::instance()->run_phase1();

		$this->assertSame( 2, $inserted );

		$model = RedirectsModel::instance();
		$row   = $model->find_exact( '/legacy-301' );
		$this->assertNotNull( $row );
		$this->assertSame( 301, (int) $row->redirect_type );
		$this->assertSame( 'https://example.com/new-301', $row->target_url );

		$row = $model->find_exact( '/legacy-302' );
		$this->assertNotNull( $row );
		$this->assertSame( 302, (int) $row->redirect_type );
	}

	/**
	 * Phase 1 is idempotent — re-running it does not duplicate rows.
	 */
	public function test_phase1_is_idempotent(): void {
		$this->create_legacy_table();
		$this->insert_legacy_row(
			array(
				'url'      => '/dup',
				'redirect' => 'https://example.com/dup',
				'options'  => maybe_serialize( array( 'type' => 301 ) ),
			)
		);

		$this->assertSame( 1, Migrator::instance()->run_phase1() );
		$this->assertSame( 0, Migrator::instance()->run_phase1() );

		// Still only one row on the v4 table.
		$row = RedirectsModel::instance()->find_exact( '/dup' );
		$this->assertNotNull( $row );
	}

	/**
	 * Unknown redirect types in the legacy `options` blob fall back to 301.
	 */
	public function test_phase1_unknown_redirect_type_falls_back_to_301(): void {
		$this->create_legacy_table();
		$this->insert_legacy_row(
			array(
				'url'      => '/weird-type',
				'redirect' => 'https://example.com/weird',
				'options'  => maybe_serialize( array( 'type' => 418 ) ),
			)
		);

		$this->assertSame( 1, Migrator::instance()->run_phase1() );

		$row = RedirectsModel::instance()->find_exact( '/weird-type' );
		$this->assertNotNull( $row );
		$this->assertSame( 301, (int) $row->redirect_type );
	}

	/**
	 * `bootstrap_on_activation` flips every flag on a fresh install
	 * (no legacy table on disk).
	 */
	public function test_bootstrap_on_fresh_install_marks_migration_done(): void {
		Migrator::instance()->bootstrap_on_activation();

		$settings = Settings::instance();
		$this->assertTrue( $settings->get( 'logs_migrated' ) );
		$this->assertTrue( $settings->get( 'phase1_done' ) );
		$this->assertTrue( $settings->get( 'legacy_table_dropped' ) );
	}

	/**
	 * `tick()` drives phase 2 to completion: legacy rows turn into v4
	 * log rows and the table is finally dropped.
	 */
	public function test_phase2_tick_migrates_logs_and_finalises(): void {
		$this->create_legacy_table();

		for ( $i = 1; $i <= 5; $i++ ) {
			$this->insert_legacy_row(
				array(
					'url'  => '/legacy-log-' . $i,
					'ref'  => 'N/A',                      // The legacy "no referrer" sentinel.
					'ip'   => '127.0.0.1',
					'ua'   => 'OldBrowser/1.0',
					'date' => gmdate( 'Y-m-d H:i:s', time() - ( $i * HOUR_IN_SECONDS ) ),
				)
			);
		}

		// One tick is enough for 5 rows — chunk size is 200.
		$status = Migrator::instance()->tick();

		$this->assertSame( 0, $status['remaining'] );
		$this->assertTrue( $status['logs_migrated'] );
		$this->assertFalse( $status['legacy_present'], 'Legacy table should be dropped after finalise.' );

		// Every legacy URL should now have a v4 log row.
		$logs = LogsModel::instance();
		for ( $i = 1; $i <= 5; $i++ ) {
			$row = $logs->get_by_url( '/legacy-log-' . $i );
			$this->assertNotNull( $row, "Missing migrated log /legacy-log-$i" );
			// "N/A" referer should be normalised to empty.
			$this->assertSame( '', (string) $row->ref );
		}
	}

	/**
	 * v3 stored per-URL overrides in the serialised `options` blob.
	 * Phase 2 must carry them onto the v4 logs table, mapping the
	 * tri-state `{-1 → GLOBAL, 1 → ENABLE, 0 → DISABLE}` and silently
	 * dropping the `log` override (no v4 equivalent).
	 */
	public function test_phase2_migrates_v3_overrides_to_v4_columns(): void {
		$this->create_legacy_table();

		$this->insert_legacy_row(
			array(
				'url'     => '/v3-disable-both',
				'options' => maybe_serialize(
					array(
						'redirect' => 0,    // v3 DISABLE
						'log'      => 0,    // dropped silently in v4
						'alert'    => 0,    // v3 DISABLE
					)
				),
			)
		);
		$this->insert_legacy_row(
			array(
				'url'     => '/v3-enable-redirect',
				'options' => maybe_serialize(
					array(
						'redirect' => 1,    // v3 ENABLE
						'alert'    => -1,   // v3 sentinel for GLOBAL
					)
				),
			)
		);
		$this->insert_legacy_row(
			array(
				'url'     => '/v3-no-options',
				'options' => null,
			)
		);

		Migrator::instance()->tick();

		$logs = LogsModel::instance();

		$disabled = $logs->get_by_url( '/v3-disable-both' );
		$this->assertNotNull( $disabled );
		$this->assertSame( LogsModel::OVERRIDE_DISABLE, (int) $disabled->override_redirect );
		$this->assertSame( LogsModel::OVERRIDE_DISABLE, (int) $disabled->override_email );

		$enable = $logs->get_by_url( '/v3-enable-redirect' );
		$this->assertNotNull( $enable );
		$this->assertSame( LogsModel::OVERRIDE_ENABLE, (int) $enable->override_redirect );
		$this->assertSame( LogsModel::OVERRIDE_GLOBAL, (int) $enable->override_email );

		// Missing options blob → both columns stay at the default.
		$plain = $logs->get_by_url( '/v3-no-options' );
		$this->assertNotNull( $plain );
		$this->assertSame( LogsModel::OVERRIDE_GLOBAL, (int) $plain->override_redirect );
		$this->assertSame( LogsModel::OVERRIDE_GLOBAL, (int) $plain->override_email );
	}

	/**
	 * `abort()` cancels the migration in flight — the table stays put
	 * for forensics, but `logs_migrated` flips to true so we stop.
	 */
	public function test_abort_marks_done_without_dropping_legacy_table(): void {
		$this->create_legacy_table();
		$this->insert_legacy_row( array( 'url' => '/keep-me' ) );

		$status = Migrator::instance()->abort();

		$this->assertTrue( $status['logs_migrated'] );
		$this->assertTrue( $status['legacy_present'] );
		$this->assertSame( 1, $status['remaining'] );
	}

	/**
	 * A not-yet-started migration is NOT reported as running, even when
	 * legacy data is present — `running` tracks an in-flight migration,
	 * not the mere presence of unmigrated data. This keeps the banner in
	 * its idle "Start migration" state (where the optional Action
	 * Scheduler install offer lives).
	 */
	public function test_status_not_running_until_started(): void {
		$this->create_legacy_table();
		$this->insert_legacy_row( array( 'url' => '/x' ) );

		$status = Migrator::instance()->status();

		$this->assertTrue( $status['legacy_present'] );
		$this->assertFalse( $status['running'] );
		$this->assertSame( 1, $status['remaining'] );
	}

	/**
	 * Once a migration has been started (the `migration_started` flag is
	 * set) and the logs aren't migrated yet, `status()` reports running.
	 */
	public function test_status_running_once_started(): void {
		$this->create_legacy_table();
		$this->insert_legacy_row( array( 'url' => '/x' ) );

		Settings::instance()->set( 'migration_started', true );

		$status = Migrator::instance()->status();

		$this->assertTrue( $status['legacy_present'] );
		$this->assertTrue( $status['running'] );
	}
}
