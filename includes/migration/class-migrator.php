<?php
/**
 * v3 → v4 data migrator.
 *
 * Migrates the legacy `wp_404_to_301` table (single table holding both
 * log rows and the custom-redirect column) into the two new BerlinDB
 * tables (`logs` + `redirects`).
 *
 * Two phases:
 *
 *   1. Auto — rows that carry a non-empty `redirect` column become
 *      `404_to_301_redirects` rows. Runs unattended (no admin
 *      consent) because moving a handful of custom redirects is the
 *      kind of thing users expect to "just work".
 *
 *   2. Opt-in — every remaining row migrates into `404_to_301_logs`.
 *      Can be thousands of rows on busy sites, so processed in chunks
 *      via {@see Scheduler::queue_next_chunk()} until the legacy
 *      table is empty. Triggered by the admin clicking "Start
 *      migration" in the Logs page banner.
 *
 * The legacy table is dropped only when phase 2 completes — until
 * then it is the source of truth, so we can resume cleanly after a
 * deactivation / fatal / crash.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Migration;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Models\Logs as LogsModel;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;
use DuckDev\FourNotFour\Settings;
use DuckDev\FourNotFour\Utils\Helpers;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Migrator
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Migration
 */
class Migrator extends Singleton {

	/**
	 * Rows processed per chunk when phase 2 is running.
	 *
	 * Conservative default that finishes in well under PHP's
	 * `max_execution_time` even on shared hosts.
	 *
	 * @since 4.0.0
	 */
	const CHUNK_SIZE = 200;

	/**
	 * Wire the scheduler hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( Scheduler::ACTION, array( $this, 'run_chunk' ) );
	}

	/**
	 * Bootstrap migration state during activation.
	 *
	 * Called from {@see \DuckDev\FourNotFour\Setup\Activator::run()}.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function bootstrap_on_activation(): void {
		$settings = Settings::instance();

		// Fresh install: nothing to migrate.
		if ( ! $this->legacy_table_exists() ) {
			$settings->set( 'logs_migrated', true );
			$settings->set( 'phase1_done', true );
			$settings->set( 'legacy_table_dropped', true );
			return;
		}

		// Phase 1 — move the custom-redirect rows now (small, cheap).
		if ( ! $settings->get( 'phase1_done', false ) ) {
			$this->run_phase1();
			$settings->set( 'phase1_done', true );
		}
	}

	/**
	 * Pause queued background jobs without forgetting our state.
	 *
	 * Called from the deactivation handler. Phase 2 resumes on next
	 * activation by checking `logs_migrated` again.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function pause(): void {
		Scheduler::cancel_all();
	}

	/**
	 * Phase 1 — copy every legacy row with a non-empty `redirect`
	 * column into the new redirects table.
	 *
	 * Idempotent: rows whose source already exists are skipped.
	 *
	 * @since 4.0.0
	 *
	 * @return int Number of redirects inserted.
	 */
	public function run_phase1(): int {
		global $wpdb;

		$table = $wpdb->prefix . '404_to_301';
		$rows  = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT url, redirect, options FROM {$table} WHERE redirect IS NOT NULL AND redirect <> ''"
		);

		if ( empty( $rows ) ) {
			return 0;
		}

		$model    = RedirectsModel::instance();
		$inserted = 0;

		foreach ( (array) $rows as $row ) {
			$source = (string) $row->url;
			$target = (string) $row->redirect;

			if ( '' === $source || '' === $target ) {
				continue;
			}

			// Skip if the source has already been migrated.
			if ( $model->find_exact( $source ) ) {
				continue;
			}

			$options = $row->options ? maybe_unserialize( $row->options ) : array();
			$type    = isset( $options['type'] ) ? (int) $options['type'] : 301;

			$id = $model->create(
				array(
					'source'        => $source,
					'match_type'    => 'exact',
					'target_type'   => 'link',
					'target_url'    => $target,
					'redirect_type' => in_array( $type, array( 301, 302, 307 ), true ) ? $type : 301,
					'is_active'     => 1,
				)
			);

			if ( $id > 0 ) {
				++$inserted;
			}
		}

		return $inserted;
	}

	/**
	 * Start phase 2 — opt-in chunked log migration.
	 *
	 * Processes one chunk synchronously so the React UI sees immediate
	 * progress, then also queues a background continuation. The React
	 * UI polls `/migration` (which calls {@see Migrator::tick()})
	 * until `remaining` reaches 0 — that means the migration finishes
	 * even on dev environments where wp-cron / AS aren't firing.
	 *
	 * @since 4.0.0
	 *
	 * @return array Migration status snapshot.
	 */
	public function start_phase2(): array {
		if ( ! $this->legacy_table_exists() ) {
			Settings::instance()->set( 'logs_migrated', true );
			return $this->status();
		}

		// Best-effort: schedule a background continuation so the
		// migration completes even if the admin closes the tab.
		Scheduler::queue_next_chunk();

		// Process one chunk inline so the polling loop has fresh
		// numbers to display straight away — and so the migration
		// keeps moving even when wp-cron is broken.
		$this->run_chunk();

		return $this->status();
	}

	/**
	 * Process one chunk on demand.
	 *
	 * Driven by the React UI's poll loop. Each POST `/migration`
	 * (after the initial start) calls this, processes one chunk and
	 * returns the updated status.
	 *
	 * @since 4.0.0
	 *
	 * @return array Migration status snapshot after the tick.
	 */
	public function tick(): array {
		$this->run_chunk();

		// Keep the background continuation primed in case the tab
		// closes before the next React tick.
		if ( ! Settings::instance()->get( 'logs_migrated', false ) && $this->remaining_rows() > 0 ) {
			Scheduler::queue_next_chunk();
		}

		return $this->status();
	}

	/**
	 * Abort an in-flight migration.
	 *
	 * Cancels the queue and marks the migration as complete (the
	 * legacy table is left in place for forensic / re-try purposes).
	 *
	 * @since 4.0.0
	 *
	 * @return array Status snapshot.
	 */
	public function abort(): array {
		Scheduler::cancel_all();
		Settings::instance()->set( 'logs_migrated', true );

		return $this->status();
	}

	/**
	 * Run a single chunk. Hooked into {@see Scheduler::ACTION}.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function run_chunk(): void {
		global $wpdb;

		$settings = Settings::instance();

		if ( $settings->get( 'logs_migrated', false ) ) {
			return;
		}

		if ( ! $this->legacy_table_exists() ) {
			$settings->set( 'logs_migrated', true );
			return;
		}

		$table = $wpdb->prefix . '404_to_301';
		$rows  = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->prepare(
				"SELECT id, url, ref, ip, ua, date FROM {$table} ORDER BY id ASC LIMIT %d",
				self::CHUNK_SIZE
			)
		);

		if ( empty( $rows ) ) {
			$this->finalise();
			return;
		}

		$logs     = LogsModel::instance();
		$processed_ids = array();

		foreach ( (array) $rows as $row ) {
			$logs->record_hit(
				array(
					'url'        => (string) $row->url,
					'ref'        => (string) $row->ref,
					'ip'         => Helpers::pack_ip( (string) $row->ip ),
					'ua'         => (string) $row->ua,
					'method'     => 'GET',
					'created_at' => $row->date ?: current_time( 'mysql', true ),
				)
			);

			$processed_ids[] = (int) $row->id;
		}

		// Delete the rows we just migrated so the next chunk picks
		// up the next batch and we resume cleanly across restarts.
		if ( ! empty( $processed_ids ) ) {
			$placeholders = implode( ',', array_fill( 0, count( $processed_ids ), '%d' ) );
			$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->prepare(
					"DELETE FROM {$table} WHERE id IN ({$placeholders})",
					$processed_ids
				)
			);
		}

		// More rows to process? Queue the next chunk; otherwise tidy up.
		$remaining = $this->remaining_rows();

		if ( $remaining > 0 ) {
			Scheduler::queue_next_chunk();
		} else {
			$this->finalise();
		}
	}

	/**
	 * Build a snapshot of the migration state for the React banner.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function status(): array {
		$settings = Settings::instance();
		$running  = $this->legacy_table_exists() && ! $settings->get( 'logs_migrated', false );

		return array(
			'phase1_done'        => (bool) $settings->get( 'phase1_done', false ),
			'logs_migrated'      => (bool) $settings->get( 'logs_migrated', false ),
			'legacy_present'     => $this->legacy_table_exists(),
			'remaining'          => $this->remaining_rows(),
			'running'            => $running,
			'has_as'             => Scheduler::has_action_scheduler(),
			'can_install_as'     => current_user_can( 'install_plugins' )
				&& ! ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ),
			'install_as_url'     => Scheduler::install_as_url(),
		);
	}

	/**
	 * Whether the legacy v3 table is in the database.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function legacy_table_exists(): bool {
		global $wpdb;

		$table  = $wpdb->prefix . '404_to_301';
		$exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) )
		);

		return $table === $exists;
	}

	/**
	 * How many rows are left in the legacy table.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function remaining_rows(): int {
		global $wpdb;

		if ( ! $this->legacy_table_exists() ) {
			return 0;
		}

		$table = $wpdb->prefix . '404_to_301';

		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Finish the migration: drop the legacy table, flag completion,
	 * fire the completion action.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function finalise(): void {
		global $wpdb;

		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}404_to_301" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		// Legacy options become safe to delete once their data has
		// been migrated.
		delete_option( 'i4t3_gnrl_options' );
		delete_option( 'i4t3_db_version' );
		delete_option( 'i4t3_version_no' );
		delete_option( 'i4t3_review_notice' );
		delete_option( 'i4t3_activated_time' );

		$settings = Settings::instance();
		$settings->set( 'logs_migrated', true );
		$settings->set( 'legacy_table_dropped', true );

		/**
		 * Fires once the migration finishes successfully.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_migration_complete' );
	}
}
