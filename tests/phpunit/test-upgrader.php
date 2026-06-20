<?php
/**
 * Tests for the {@see \DuckDev\FourNotFour\Setup\Upgrader} version-bump
 * routines — specifically the 4.0.1 status-3 → status-0/2 migration.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Models\Logs;
use DuckDev\FourNotFour\Models\Redirects;
use DuckDev\FourNotFour\Setup\Upgrader;

/**
 * Class UpgraderTest
 *
 * @group upgrader
 */
class UpgraderTest extends WP_UnitTestCase {

	public function set_up(): void {
		parent::set_up();
		Database::instance();
	}

	/**
	 * Existing log rows carrying the legacy STATUS_CUSTOM (3) value must
	 * be converted: linked redirect active → 2 (Fixed), inactive or no
	 * link → 0 (Open).
	 */
	public function test_to_4_0_1_migrates_legacy_status_3_rows(): void {
		global $wpdb;

		$logs      = Logs::instance();
		$redirects = Redirects::instance();

		// Active redirect → its linked status=3 row should become Fixed.
		$active_id = $redirects->create(
			array(
				'source'      => '/active-source',
				'target_url'  => 'https://example.com/a',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		// Inactive redirect → its linked status=3 row should become Open.
		$inactive_id = $redirects->create(
			array(
				'source'      => '/inactive-source',
				'target_url'  => 'https://example.com/i',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 0,
			)
		);

		$linked_to_active   = $logs->record_hit( array( 'url' => '/linked-active' ) );
		$linked_to_inactive = $logs->record_hit( array( 'url' => '/linked-inactive' ) );
		$unlinked           = $logs->record_hit( array( 'url' => '/unlinked' ) );

		// Force status=3 directly — `set_status` rejects it (the value
		// no longer exists in the v4.0.1 enum, which is the whole point
		// of the migration).
		$logs_table = $wpdb->prefix . '404_to_301_logs';
		$wpdb->update( $logs_table, array( 'status' => 3, 'redirect_id' => $active_id ), array( 'id' => $linked_to_active ) );
		$wpdb->update( $logs_table, array( 'status' => 3, 'redirect_id' => $inactive_id ), array( 'id' => $linked_to_inactive ) );
		$wpdb->update( $logs_table, array( 'status' => 3 ), array( 'id' => $unlinked ) );

		// Pretend the site is upgrading from 4.0.0 → trigger the upgrade
		// path. `maybe_upgrade` is normally hooked on admin_init; calling
		// it directly is the supported test seam.
		update_option( Upgrader::VERSION_KEY, '4.0.0', false );
		Upgrader::instance()->maybe_upgrade();

		// Bust the BerlinDB row cache so `find()` reads from the DB and
		// reflects the direct UPDATE the migration just performed.
		wp_cache_delete( $linked_to_active, '404_to_301_logs' );
		wp_cache_delete( $linked_to_inactive, '404_to_301_logs' );
		wp_cache_delete( $unlinked, '404_to_301_logs' );

		$this->assertSame( Logs::STATUS_FIXED, (int) $logs->find( $linked_to_active )->status );
		$this->assertSame( Logs::STATUS_OPEN, (int) $logs->find( $linked_to_inactive )->status );
		$this->assertSame( Logs::STATUS_OPEN, (int) $logs->find( $unlinked )->status );

		// Version was stamped to current — running again is a no-op.
		$this->assertSame( D404_VERSION, get_option( Upgrader::VERSION_KEY ) );
	}

	/**
	 * Running the upgrade twice doesn't re-flip rows or error out.
	 */
	public function test_to_4_0_1_is_idempotent(): void {
		global $wpdb;

		$logs = Logs::instance();
		$id   = $logs->record_hit( array( 'url' => '/idempotent' ) );

		$logs_table = $wpdb->prefix . '404_to_301_logs';
		$wpdb->update( $logs_table, array( 'status' => 3 ), array( 'id' => $id ) );

		update_option( Upgrader::VERSION_KEY, '4.0.0', false );
		Upgrader::instance()->maybe_upgrade();
		wp_cache_delete( $id, '404_to_301_logs' );

		$this->assertSame( Logs::STATUS_OPEN, (int) $logs->find( $id )->status );

		// Second run: stored version is now current, so maybe_upgrade
		// returns immediately. The row stays Open.
		Upgrader::instance()->maybe_upgrade();
		wp_cache_delete( $id, '404_to_301_logs' );
		$this->assertSame( Logs::STATUS_OPEN, (int) $logs->find( $id )->status );
	}
}
