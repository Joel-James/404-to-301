<?php
/**
 * Log-action tests under different settings.
 *
 * Verifies that `logs_enabled`, `logs_skip_bots`,
 * `logs_skip_duplicates`, `exclude_paths` and `logs_retention_days`
 * each shape what the Log action and prune routine actually do.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Front\Actions\Log;
use DuckDev\FourNotFour\Front\Request;
use DuckDev\FourNotFour\Models\Logs as LogsModel;
use DuckDev\FourNotFour\Settings;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class LogsConfigTest
 *
 * @group logs
 */
class LogsConfigTest extends WP_UnitTestCase {

	public function set_up(): void {
		parent::set_up();
		Database::instance();

		add_filter( '404_to_301_request_is_404', '__return_true' );

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI']    = '/missing';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11) Gecko/20100101 Firefox/120.0';
	}

	public function tear_down(): void {
		remove_all_filters( '404_to_301_request_is_404' );
		// Don't unset the `$_SERVER` keys — later teardown steps
		// (eg. wp-cron) read them and emit deprecations when missing.
		parent::tear_down();
	}

	private function configure( array $overrides ): void {
		Settings::instance()->update(
			array_merge( Settings::instance()->all(), $overrides )
		);
	}

	/**
	 * `logs_enabled = false` short-circuits the action.
	 */
	public function test_logs_disabled_writes_nothing(): void {
		$this->configure( array( 'logs_enabled' => false ) );

		( new Log() )->run( new Request() );

		$this->assertNull( LogsModel::instance()->get_by_url( '/missing' ) );
	}

	/**
	 * `logs_skip_bots = true` (the default) skips obvious crawler UAs.
	 */
	public function test_skip_bots_filters_crawler_ua(): void {
		$this->configure( array( 'logs_enabled' => true, 'logs_skip_bots' => true ) );

		$_SERVER['HTTP_USER_AGENT'] = 'Googlebot/2.1 (+http://www.google.com/bot.html)';

		( new Log() )->run( new Request() );

		$this->assertNull( LogsModel::instance()->get_by_url( '/missing' ) );
	}

	/**
	 * Bots are logged when `logs_skip_bots = false`.
	 */
	public function test_bots_logged_when_skip_bots_disabled(): void {
		$this->configure( array( 'logs_enabled' => true, 'logs_skip_bots' => false ) );

		$_SERVER['HTTP_USER_AGENT'] = 'Googlebot/2.1';

		( new Log() )->run( new Request() );

		$this->assertNotNull( LogsModel::instance()->get_by_url( '/missing' ) );
	}

	/**
	 * `logs_skip_duplicates = true` leaves an existing row untouched —
	 * the hit counter is NOT incremented on the second call.
	 */
	public function test_skip_duplicates_does_not_bump_hits(): void {
		$this->configure(
			array(
				'logs_enabled'         => true,
				'logs_skip_bots'       => false,
				'logs_skip_duplicates' => true,
			)
		);

		( new Log() )->run( new Request() );
		( new Log() )->run( new Request() );

		$row = LogsModel::instance()->get_by_url( '/missing' );
		$this->assertNotNull( $row );
		$this->assertSame( 1, (int) $row->hits, 'Second hit should be skipped, not bumped.' );
	}

	/**
	 * Default behaviour (skip_duplicates off) bumps the counter.
	 */
	public function test_default_bumps_hits_on_repeat(): void {
		$this->configure(
			array(
				'logs_enabled'         => true,
				'logs_skip_bots'       => false,
				'logs_skip_duplicates' => false,
			)
		);

		( new Log() )->run( new Request() );
		( new Log() )->run( new Request() );
		( new Log() )->run( new Request() );

		$row = LogsModel::instance()->get_by_url( '/missing' );
		$this->assertNotNull( $row );
		$this->assertSame( 3, (int) $row->hits );
	}

	/**
	 * URLs matching an `exclude_paths` entry never reach the table.
	 */
	public function test_excluded_path_is_not_logged(): void {
		$this->configure(
			array(
				'logs_enabled'         => true,
				'logs_skip_bots'       => false,
				'exclude_paths'        => array( '/missing' ),
			)
		);

		( new Log() )->run( new Request() );

		$this->assertNull( LogsModel::instance()->get_by_url( '/missing' ) );
	}

	/**
	 * `Logs::prune()` deletes rows older than the configured retention.
	 */
	public function test_prune_honours_retention_days(): void {
		$model = LogsModel::instance();
		$now   = current_time( 'mysql', true );
		$old   = gmdate( 'Y-m-d H:i:s', time() - ( 30 * DAY_IN_SECONDS ) );

		// Insert each row directly with the desired `created_at` rather
		// than relying on `record_hit` + a follow-up `update` — that
		// two-step dance is brittle when the same column is touched
		// from two sides of BerlinDB's row cache.
		$insert = static function ( string $url, string $created ) use ( $model, $now ): int {
			return $model->create(
				array(
					'url'        => $url,
					'url_hash'   => Helpers::url_hash( $url ),
					'hits'       => 1,
					'status'     => LogsModel::STATUS_OPEN,
					'created_at' => $created,
					'updated_at' => $now,
				)
			);
		};

		$old_1 = $insert( '/old-1', $old );
		$old_2 = $insert( '/old-2', $old );
		$fresh = $insert( '/fresh', $now );

		$this->assertSame( 2, $model->prune( 7 ) );

		$this->assertNull( $model->find( $old_1 ) );
		$this->assertNull( $model->find( $old_2 ) );
		$this->assertNotNull( $model->find( $fresh ) );
	}

	/**
	 * Filter `404_to_301_pre_log_insert` can rewrite the row data
	 * before it is persisted.
	 */
	public function test_pre_log_insert_filter_can_rewrite_data(): void {
		$this->configure(
			array(
				'logs_enabled'   => true,
				'logs_skip_bots' => false,
			)
		);

		add_filter(
			'404_to_301_pre_log_insert',
			static function ( $data ) {
				$data['ua'] = 'rewritten-by-filter';
				return $data;
			}
		);

		( new Log() )->run( new Request() );

		remove_all_filters( '404_to_301_pre_log_insert' );

		$row = LogsModel::instance()->get_by_url( '/missing' );
		$this->assertNotNull( $row );
		$this->assertSame( 'rewritten-by-filter', (string) $row->ua );
	}
}
