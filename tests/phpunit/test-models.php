<?php
/**
 * Tests for the {@see \DuckDev\FourNotFour\Models\Logs} and
 * {@see \DuckDev\FourNotFour\Models\Redirects} facades.
 *
 * @package DuckDev\FourNotFour
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

	/**
	 * Boot the BerlinDB-backed tables before each test runs.
	 */
	public function set_up(): void {
		parent::set_up();

		Database::instance();

		// `WP_UnitTestCase` wraps every test in a DB transaction and
		// rolls it back in `tear_down()`, so inserts into the plugin
		// tables get cleaned up automatically. We intentionally do NOT
		// override `tear_down()` here — a manual `TRUNCATE` would issue
		// an implicit commit and break the rollback.
	}

	/**
	 * First `record_hit` inserts; the second on the same URL bumps `hits`.
	 */
	public function test_logs_record_hit_inserts_then_bumps(): void {
		$model = Logs::instance();
		$id    = $model->record_hit(
			array(
				'url' => '/missing',
				'ua'  => 'browser',
			)
		);

		$this->assertGreaterThan( 0, $id );

		$row = $model->find( $id );
		$this->assertSame( 1, (int) $row->hits );

		// Second hit on the same URL bumps `hits` instead of inserting a new row.
		$same = $model->record_hit(
			array(
				'url' => '/missing',
				'ua'  => 'browser',
			)
		);
		$this->assertSame( $id, $same );

		$row = $model->find( $id );
		$this->assertSame( 2, (int) $row->hits );
	}

	/**
	 * Trailing slash and case differences collapse onto the same log row.
	 */
	public function test_logs_url_normalisation(): void {
		$model = Logs::instance();
		$first = $model->record_hit( array( 'url' => '/foo' ) );
		$same  = $model->record_hit( array( 'url' => '/Foo/' ) );

		$this->assertSame( $first, $same, 'Trailing slash + case should normalise into the same row.' );
	}

	/**
	 * `set_status` writes the status column for a valid value.
	 */
	public function test_logs_set_status(): void {
		$model = Logs::instance();
		$id    = $model->record_hit( array( 'url' => '/fix-me' ) );

		$this->assertTrue( $model->set_status( $id, Logs::STATUS_FIXED ) );

		$row = $model->find( $id );
		$this->assertSame( Logs::STATUS_FIXED, (int) $row->status );
	}

	/**
	 * Creating a redirect persists it and exposes it via `find_exact()`.
	 */
	public function test_redirects_create_and_find_exact(): void {
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'        => '/old-page',
				'target_url'    => 'https://example.com/new',
				'target_type'   => 'link',
				'match_type'    => 'exact',
				'redirect_type' => 301,
				'is_active'     => 1,
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

	/**
	 * Prefix rules match URLs that start with the rule's source.
	 */
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

	/**
	 * Regex rules match URLs that satisfy the pattern.
	 */
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

	/**
	 * `record_hit` is a no-op when no URL is supplied.
	 */
	public function test_logs_record_hit_skips_empty_url(): void {
		$this->assertSame( 0, Logs::instance()->record_hit( array( 'url' => '' ) ) );
		$this->assertSame( 0, Logs::instance()->record_hit( array() ) );
	}

	/**
	 * `set_status` refuses any value outside the STATUS_* constants.
	 */
	public function test_logs_set_status_rejects_unknown_value(): void {
		$model = Logs::instance();
		$id    = $model->record_hit( array( 'url' => '/bad-status' ) );

		$this->assertFalse( $model->set_status( $id, 99 ) );
	}

	/**
	 * Linking a redirect flips status to CUSTOM; unlinking sends it back to OPEN.
	 */
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

	/**
	 * Unknown override values are coerced back to OVERRIDE_GLOBAL.
	 */
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

	/**
	 * `prune` deletes rows older than the cutoff; ≤0 days is a no-op.
	 */
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

	/**
	 * `find_match` resolves exact > prefix > regex when multiple rules overlap.
	 */
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

	/**
	 * Inactive rows are invisible to `find_match`.
	 */
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

	/**
	 * `record_hit` returns false when the redirect id doesn't exist.
	 */
	public function test_redirects_record_hit_returns_false_for_missing_id(): void {
		$this->assertFalse( Redirects::instance()->record_hit( 999999 ) );
	}

	/**
	 * Changing `source` refreshes the `source_hash` so `find_exact` follows.
	 */
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

	/**
	 * Each `record_hit` bumps the counter and updates `last_hit_at`.
	 */
	public function test_redirects_record_hit_bumps_counter(): void {
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/x',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$model->record_hit( $id );
		$model->record_hit( $id );

		$row = $model->find( $id );
		$this->assertSame( 2, (int) $row->hits );
		$this->assertNotNull( $row->last_hit_at );
	}

	/* ---------------------------------------------------------------- *
	 * Audit trail (#5)
	 * ---------------------------------------------------------------- */

	/**
	 * `create()` stamps `modified_by` from the current user.
	 */
	public function test_redirects_create_stamps_modified_by(): void {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/audited-create',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$row = $model->find( $id );
		$this->assertSame( $user_id, (int) $row->modified_by );

		wp_set_current_user( 0 );
	}

	/**
	 * `update()` re-stamps `modified_by` to the editing user.
	 */
	public function test_redirects_update_restamps_modified_by(): void {
		$creator = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$editor  = self::factory()->user->create( array( 'role' => 'administrator' ) );

		wp_set_current_user( $creator );
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/audited-update',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		wp_set_current_user( $editor );
		$model->update( $id, array( 'target_url' => 'https://example.com/new' ) );

		$row = $model->find( $id );
		$this->assertSame( $editor, (int) $row->modified_by );

		wp_set_current_user( 0 );
	}

	/**
	 * `record_hit()` is a public side-effect — it must not masquerade
	 * as a user edit by overwriting `modified_by`.
	 */
	public function test_redirects_record_hit_does_not_change_modified_by(): void {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/no-stamp-on-hit',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		wp_set_current_user( 0 );
		$model->record_hit( $id );

		$row = $model->find( $id );
		$this->assertSame( $user_id, (int) $row->modified_by );
	}

	/**
	 * The `404_to_301_redirect_audit` action fires on create / update /
	 * delete with the expected payload.
	 */
	public function test_redirect_audit_action_fires_on_each_mutation(): void {
		$events = array();
		$listener = static function ( $action, $id, $user_id, $data ) use ( &$events ) {
			$events[] = compact( 'action', 'id', 'user_id', 'data' );
		};

		add_action( '404_to_301_redirect_audit', $listener, 10, 4 );

		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/audit-hook',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$model->update( $id, array( 'is_active' => 0 ) );
		$model->delete( $id );

		remove_action( '404_to_301_redirect_audit', $listener, 10 );
		wp_set_current_user( 0 );

		$this->assertCount( 3, $events );
		$this->assertSame( 'created', $events[0]['action'] );
		$this->assertSame( 'updated', $events[1]['action'] );
		$this->assertSame( 'deleted', $events[2]['action'] );

		foreach ( $events as $event ) {
			$this->assertSame( $id, (int) $event['id'] );
			$this->assertSame( $user_id, (int) $event['user_id'] );
		}

		// Delete fires with an empty data array.
		$this->assertSame( array(), $events[2]['data'] );
	}

	/**
	 * Caller-supplied `modified_by` wins over `get_current_user_id()`
	 * so WP-CLI / migrations can attribute writes deliberately.
	 */
	public function test_redirects_create_preserves_caller_modified_by(): void {
		$current = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$author  = self::factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $current );

		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/explicit-author',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
				'modified_by' => $author,
			)
		);

		$row = $model->find( $id );
		$this->assertSame( $author, (int) $row->modified_by );

		wp_set_current_user( 0 );
	}

	/* ---------------------------------------------------------------- *
	 * Per-redirect query handling (#6)
	 * ---------------------------------------------------------------- */

	/**
	 * `require` rows hash the query string and so coexist as two
	 * distinct rows under the same path.
	 */
	public function test_redirects_require_mode_allows_query_variants_to_coexist(): void {
		$model = Redirects::instance();

		$summer_id = $model->create(
			array(
				'source'         => '/promo?code=summer',
				'target_url'     => 'https://example.com/summer',
				'target_type'    => 'link',
				'match_type'     => 'exact',
				'query_handling' => 'require',
				'is_active'      => 1,
			)
		);

		$winter_id = $model->create(
			array(
				'source'         => '/promo?code=winter',
				'target_url'     => 'https://example.com/winter',
				'target_type'    => 'link',
				'match_type'     => 'exact',
				'query_handling' => 'require',
				'is_active'      => 1,
			)
		);

		$this->assertGreaterThan( 0, $summer_id );
		$this->assertGreaterThan( 0, $winter_id );
		$this->assertNotSame( $summer_id, $winter_id );

		$summer = $model->find_exact( '/promo?code=summer' );
		$winter = $model->find_exact( '/promo?code=winter' );

		$this->assertNotNull( $summer );
		$this->assertNotNull( $winter );
		$this->assertSame( $summer_id, (int) $summer->id );
		$this->assertSame( $winter_id, (int) $winter->id );
	}

	/**
	 * When a request carries a query that doesn't match a `require`
	 * row, lookup falls back to an `ignore` row stored on the same
	 * path.
	 */
	public function test_redirects_find_exact_falls_back_to_ignore_row(): void {
		$model = Redirects::instance();

		$specific = $model->create(
			array(
				'source'         => '/promo?code=summer',
				'target_url'     => 'https://example.com/summer',
				'target_type'    => 'link',
				'match_type'     => 'exact',
				'query_handling' => 'require',
				'is_active'      => 1,
			)
		);

		$generic = $model->create(
			array(
				'source'         => '/promo',
				'target_url'     => 'https://example.com/promo',
				'target_type'    => 'link',
				'match_type'     => 'exact',
				'query_handling' => 'ignore',
				'is_active'      => 1,
			)
		);

		$match_specific = $model->find_exact( '/promo?code=summer' );
		$match_generic  = $model->find_exact( '/promo?code=other' );

		$this->assertSame( $specific, (int) $match_specific->id );
		$this->assertSame( $generic, (int) $match_generic->id );
	}

	/**
	 * Flipping `query_handling` on an existing row refreshes its hash
	 * so the row still matches correctly afterwards.
	 */
	public function test_redirects_update_query_handling_refreshes_hash(): void {
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'         => '/swappy?a=1',
				'target_url'     => 'https://example.com/',
				'target_type'    => 'link',
				'match_type'     => 'exact',
				'query_handling' => 'ignore',
				'is_active'      => 1,
			)
		);

		// In `ignore` mode the row is stored under the path-only hash.
		$this->assertNotNull( $model->find_exact( '/swappy?a=1' ) );

		$model->update( $id, array( 'query_handling' => 'require' ) );

		// After switching to `require`, the row's hash includes the
		// query — only the exact query matches now.
		$matched = $model->find_exact( '/swappy?a=1' );
		$this->assertNotNull( $matched );
		$this->assertSame( $id, (int) $matched->id );

		// A request with a different query should not match the row.
		$this->assertNull( $model->find_exact( '/swappy?a=2' ) );
	}
}
