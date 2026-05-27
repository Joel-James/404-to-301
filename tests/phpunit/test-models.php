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
		global $wpdb;

		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}404_to_301_logs" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}404_to_301_redirects" );

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
