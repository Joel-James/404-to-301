<?php
/**
 * Tests for the {@see \DuckDev\FourNotFour\Models\Logs} and
 * {@see \DuckDev\FourNotFour\Models\Redirects} facades.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Models\Logs;
use DuckDev\FourNotFour\Models\Redirects;

/**
 * Class ModelsTest
 *
 * @group models
 */
class ModelsTest extends WP_UnitTestCase {

	public function set_up(): void {
		parent::set_up();

		Database::instance();
	}

	public function tear_down(): void {
		// `WP_UnitTestCase` wraps every test in a DB transaction and
		// rolls it back here, so inserts into the plugin tables get
		// cleaned up automatically. A manual `TRUNCATE` would issue an
		// implicit commit and break that rollback.
		parent::tear_down();
	}

	public function test_logs_record_hit_inserts_then_bumps(): void {
		$model = Logs::instance();
		$id    = $model->record_hit( array( 'url' => '/missing', 'ua' => 'browser' ) );

		$this->assertGreaterThan( 0, $id );

		$row = $model->find( $id );
		$this->assertSame( 1, (int) $row->hits );

		// Second hit on the same URL bumps `hits` instead of inserting a new row.
		$same = $model->record_hit( array( 'url' => '/missing', 'ua' => 'browser' ) );
		$this->assertSame( $id, $same );

		$row = $model->find( $id );
		$this->assertSame( 2, (int) $row->hits );
	}

	public function test_logs_url_normalisation(): void {
		$model = Logs::instance();
		$first = $model->record_hit( array( 'url' => '/foo' ) );
		$same  = $model->record_hit( array( 'url' => '/Foo/' ) );

		$this->assertSame( $first, $same, 'Trailing slash + case should normalise into the same row.' );
	}

	public function test_logs_set_status(): void {
		$model = Logs::instance();
		$id    = $model->record_hit( array( 'url' => '/fix-me' ) );

		$this->assertTrue( $model->set_status( $id, Logs::STATUS_FIXED ) );

		$row = $model->find( $id );
		$this->assertSame( Logs::STATUS_FIXED, (int) $row->status );
	}

	public function test_redirects_create_and_find_exact(): void {
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/old-page',
				'target_url'  => 'https://example.com/new',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'redirect_type' => 301,
				'is_active'   => 1,
			)
		);

		$this->assertGreaterThan( 0, $id );

		$row = $model->find_exact( '/old-page' );
		$this->assertNotNull( $row );
		$this->assertSame( 'https://example.com/new', $row->target_url );

		// Trailing slash / casing matches via the hash.
		$row = $model->find_exact( '/Old-Page/' );
		$this->assertNotNull( $row );
	}

	public function test_redirects_prefix_match(): void {
		$model = Redirects::instance();
		$model->create(
			array(
				'source'      => '/blog',
				'target_url'  => 'https://example.com/news',
				'target_type' => 'link',
				'match_type'  => 'prefix',
				'is_active'   => 1,
			)
		);

		$row = $model->find_match( '/blog/some-post' );
		$this->assertNotNull( $row );
		$this->assertSame( 'prefix', $row->match_type );

		$row = $model->find_match( '/other' );
		$this->assertNull( $row );
	}

	public function test_redirects_regex_match(): void {
		$model = Redirects::instance();
		$model->create(
			array(
				'source'      => '^/products/[0-9]+$',
				'target_url'  => 'https://example.com/shop',
				'target_type' => 'link',
				'match_type'  => 'regex',
				'is_active'   => 1,
			)
		);

		$this->assertNotNull( $model->find_match( '/products/123' ) );
		$this->assertNull( $model->find_match( '/products/abc' ) );
	}

	public function test_logs_record_hit_skips_empty_url(): void {
		$this->assertSame( 0, Logs::instance()->record_hit( array( 'url' => '' ) ) );
		$this->assertSame( 0, Logs::instance()->record_hit( array() ) );
	}

	public function test_logs_set_status_rejects_unknown_value(): void {
		$model = Logs::instance();
		$id    = $model->record_hit( array( 'url' => '/bad-status' ) );

		$this->assertFalse( $model->set_status( $id, 99 ) );
	}

	public function test_logs_link_redirect_flips_status_to_custom_and_back(): void {
		$model = Logs::instance();
		$id    = $model->record_hit( array( 'url' => '/link-me' ) );

		$this->assertTrue( $model->link_redirect( $id, 12 ) );
		$row = $model->find( $id );
		$this->assertSame( Logs::STATUS_CUSTOM, (int) $row->status );
		$this->assertSame( 12, (int) $row->redirect_id );

		// Passing 0 clears the link and resets the status back to open.
		$this->assertTrue( $model->link_redirect( $id, 0 ) );
		$row = $model->find( $id );
		$this->assertSame( Logs::STATUS_OPEN, (int) $row->status );
		$this->assertNull( $row->redirect_id );
	}

	public function test_logs_set_overrides_coerces_unknown_values(): void {
		$model = Logs::instance();
		$id    = $model->record_hit( array( 'url' => '/overrides' ) );

		$this->assertTrue(
			$model->set_overrides(
				$id,
				array(
					'override_redirect' => Logs::OVERRIDE_ENABLE,
					'override_log'      => 99,           // Unknown → falls back to GLOBAL.
					'override_email'    => Logs::OVERRIDE_DISABLE,
				)
			)
		);

		$row = $model->find( $id );
		$this->assertSame( Logs::OVERRIDE_ENABLE, (int) $row->override_redirect );
		$this->assertSame( Logs::OVERRIDE_GLOBAL, (int) $row->override_log );
		$this->assertSame( Logs::OVERRIDE_DISABLE, (int) $row->override_email );
	}

	public function test_logs_prune_removes_rows_older_than_cutoff(): void {
		$model = Logs::instance();

		$old = $model->record_hit( array( 'url' => '/old' ) );
		$new = $model->record_hit( array( 'url' => '/new' ) );

		// Backdate the first row well past the prune window.
		$old_date = gmdate( 'Y-m-d H:i:s', time() - ( 30 * DAY_IN_SECONDS ) );
		$this->assertTrue( $model->update( $old, array( 'created_at' => $old_date ) ) );

		$this->assertSame( 1, $model->prune( 7 ) );
		$this->assertNull( $model->find( $old ) );
		$this->assertNotNull( $model->find( $new ) );

		// Non-positive day counts are a no-op.
		$this->assertSame( 0, $model->prune( 0 ) );
		$this->assertSame( 0, $model->prune( -5 ) );
	}

	public function test_redirects_find_match_prefers_exact_over_prefix_and_regex(): void {
		$model = Redirects::instance();

		// Unique constraint is on `source_hash`, so each row uses a
		// distinct `source` value that still matches the target URL.
		$model->create(
			array(
				'source'      => '/fo',                       // Prefix of "/foo".
				'target_url'  => 'https://example.com/prefix',
				'target_type' => 'link',
				'match_type'  => 'prefix',
				'is_active'   => 1,
			)
		);
		$model->create(
			array(
				'source'      => '^/fo.*$',                   // Regex matches "/foo".
				'target_url'  => 'https://example.com/regex',
				'target_type' => 'link',
				'match_type'  => 'regex',
				'is_active'   => 1,
			)
		);
		$exact_id = $model->create(
			array(
				'source'      => '/foo',
				'target_url'  => 'https://example.com/exact',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$row = $model->find_match( '/foo' );
		$this->assertNotNull( $row );
		$this->assertSame( 'exact', $row->match_type );
		$this->assertSame( $exact_id, (int) $row->id );
	}

	public function test_redirects_find_match_skips_inactive_rows(): void {
		$model = Redirects::instance();
		$model->create(
			array(
				'source'      => '/disabled',
				'target_url'  => 'https://example.com/x',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 0,
			)
		);

		$this->assertNull( $model->find_match( '/disabled' ) );
	}

	public function test_redirects_record_hit_returns_false_for_missing_id(): void {
		$this->assertFalse( Redirects::instance()->record_hit( 999999 ) );
	}

	public function test_redirects_update_refreshes_source_hash(): void {
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/old-source',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$this->assertTrue( $model->update( $id, array( 'source' => '/new-source' ) ) );

		$this->assertNull( $model->find_exact( '/old-source' ) );
		$row = $model->find_exact( '/new-source' );
		$this->assertNotNull( $row );
		$this->assertSame( $id, (int) $row->id );
	}

	public function test_redirects_record_hit_bumps_counter(): void {
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'     => '/x',
				'target_url' => 'https://example.com/',
				'target_type' => 'link',
				'match_type' => 'exact',
				'is_active'  => 1,
			)
		);

		$model->record_hit( $id );
		$model->record_hit( $id );

		$row = $model->find( $id );
		$this->assertSame( 2, (int) $row->hits );
		$this->assertNotNull( $row->last_hit_at );
	}
}
