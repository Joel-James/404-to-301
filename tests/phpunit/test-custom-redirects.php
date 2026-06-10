<?php
/**
 * Custom-redirect-focused tests.
 *
 * Exercises the resolution paths that the Redirect action depends on:
 * `target_type` (link / page / none), prefix length-ordering, regex
 * delimiters, hit counter, and the log <-> redirect link maintained
 * by the Redirect action.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Database\Rows\Redirect as RedirectRow;
use DuckDev\FourNotFour\Front\Actions\Redirect;
use DuckDev\FourNotFour\Front\Request;
use DuckDev\FourNotFour\Models\Logs as LogsModel;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;
use DuckDev\FourNotFour\Settings;

/**
 * Class CustomRedirectsTest
 *
 * @group redirects
 */
class CustomRedirectsTest extends WP_UnitTestCase {

	public function set_up(): void {
		parent::set_up();
		Database::instance();

		add_filter( '404_to_301_request_is_404', '__return_true' );

		// Hook `wp_redirect` (the filter the action's `wp_safe_redirect`
		// ultimately invokes) so the action's `exit` never fires, but
		// the row's hit-counter + log-linking writes — which run before
		// `wp_safe_redirect` — still execute.
		add_filter(
			'wp_redirect',
			static function ( $location, $status ) {
				throw new \RuntimeException( $location . '|' . $status );
			},
			10,
			2
		);

		// Allow our test target hosts through `wp_validate_redirect`.
		add_filter(
			'allowed_redirect_hosts',
			static function ( $hosts ) {
				$hosts[] = 'example.com';
				return $hosts;
			}
		);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
	}

	public function tear_down(): void {
		remove_all_filters( '404_to_301_request_is_404' );
		remove_all_filters( 'wp_redirect' );
		remove_all_filters( 'allowed_redirect_hosts' );
		// Intentionally NOT unsetting `$_SERVER['REQUEST_URI']` etc. —
		// downstream cron / hooks rely on the key existing.
		parent::tear_down();
	}

	private function set_url( string $url ): void {
		$_SERVER['REQUEST_URI'] = $url;
	}

	private function enable_redirect( array $extra = array() ): void {
		Settings::instance()->update(
			array_merge(
				Settings::instance()->all(),
				array( 'redirect_enabled' => true ),
				$extra
			)
		);
	}

	private function run_redirect(): array {
		try {
			( new Redirect() )->run( new Request() );
		} catch ( \RuntimeException $e ) {
			$parts = explode( '|', $e->getMessage() );
			return array(
				'url'    => (string) ( $parts[0] ?? '' ),
				'status' => (int) ( $parts[1] ?? 0 ),
			);
		}

		return array(
			'url'    => '',
			'status' => 0,
		);
	}

	/**
	 * `target_type = page` resolves the linked post's permalink at
	 * read time.
	 */
	public function test_target_type_page_resolves_permalink(): void {
		$page_id = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_type'   => 'page',
				'post_title'  => 'Target',
			)
		);

		RedirectsModel::instance()->create(
			array(
				'source'         => '/old-page',
				'target_type'    => 'page',
				'target_page_id' => $page_id,
				'match_type'     => 'exact',
				'redirect_type'  => 301,
				'is_active'      => 1,
			)
		);

		$this->enable_redirect();
		$this->set_url( '/old-page' );

		$result = $this->run_redirect();

		$this->assertSame( get_permalink( $page_id ), $result['url'] );
	}

	/**
	 * `target_type = none` on the row resolves to empty, so the
	 * Redirect action aborts before sending headers.
	 */
	public function test_target_type_none_aborts_redirect(): void {
		RedirectsModel::instance()->create(
			array(
				'source'      => '/no-go',
				'target_type' => 'none',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$this->enable_redirect();
		$this->set_url( '/no-go' );

		// No exception means no `pre_redirect` action fired — abort path hit.
		$result = $this->run_redirect();
		$this->assertSame( '', $result['url'] );
	}

	/**
	 * Bare regex sources are wrapped in `#…#` and still match.
	 */
	public function test_regex_match_with_delimiters_and_bare(): void {
		$model = RedirectsModel::instance();

		// Backslash escape sequences (`\d`) survive a round-trip through
		// MySQL with NO_BACKSLASH_ESCAPES off, but `[0-9]` is unambiguous
		// regardless of SQL mode and keeps this test resilient.
		$model->create(
			array(
				'source'      => '#^/products/[0-9]+$#',
				'target_url'  => 'https://example.com/shop',
				'target_type' => 'link',
				'match_type'  => 'regex',
				'is_active'   => 1,
			)
		);
		$model->create(
			array(
				'source'      => '^/blog-[a-z]+$',          // bare — auto-wrapped.
				'target_url'  => 'https://example.com/news',
				'target_type' => 'link',
				'match_type'  => 'regex',
				'is_active'   => 1,
			)
		);

		$this->assertInstanceOf( RedirectRow::class, $model->find_match( '/products/42' ) );
		$this->assertInstanceOf( RedirectRow::class, $model->find_match( '/blog-news' ) );
		$this->assertNull( $model->find_match( '/blog-123' ) );
	}

	/**
	 * Longer prefixes beat shorter ones because the query orders by
	 * `source DESC`.
	 */
	public function test_prefix_match_prefers_longer_source(): void {
		$model = RedirectsModel::instance();

		$short = $model->create(
			array(
				'source'      => '/news',
				'target_url'  => 'https://example.com/short',
				'target_type' => 'link',
				'match_type'  => 'prefix',
				'is_active'   => 1,
			)
		);
		$long = $model->create(
			array(
				'source'      => '/news/sports',
				'target_url'  => 'https://example.com/long',
				'target_type' => 'link',
				'match_type'  => 'prefix',
				'is_active'   => 1,
			)
		);

		$hit = $model->find_match( '/news/sports/headline' );
		$this->assertNotNull( $hit );
		$this->assertSame( $long, (int) $hit->id );
		$this->assertNotSame( $short, (int) $hit->id );
	}

	/**
	 * The Redirect action bumps the row's hit counter and links the
	 * just-written log to the matched redirect.
	 */
	public function test_redirect_action_links_log_and_bumps_hits(): void {
		$id = RedirectsModel::instance()->create(
			array(
				'source'      => '/linked',
				'target_url'  => 'https://example.com/linked',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		// Pre-write a log row so the Redirect action has something to link.
		$log_id = LogsModel::instance()->record_hit( array( 'url' => '/linked' ) );

		$this->enable_redirect();
		$this->set_url( '/linked' );

		$result = $this->run_redirect();
		$this->assertSame( 'https://example.com/linked', $result['url'] );

		$row = RedirectsModel::instance()->find( $id );
		$this->assertSame( 1, (int) $row->hits );
		$this->assertNotNull( $row->last_hit_at );

		$log = LogsModel::instance()->find( $log_id );
		$this->assertSame( $id, (int) $log->redirect_id );
		$this->assertSame( LogsModel::STATUS_CUSTOM, (int) $log->status );
	}

	/**
	 * No row matches and `target = none` — global fallback is empty,
	 * so the action aborts cleanly.
	 */
	public function test_global_fallback_none_aborts(): void {
		$this->enable_redirect( array( 'redirect_target' => 'none' ) );
		$this->set_url( '/anything' );

		$result = $this->run_redirect();
		$this->assertSame( '', $result['url'] );
	}

	/**
	 * Per-row redirects fire even when the global `redirect_enabled`
	 * toggle is off — that setting only gates the catch-all 404
	 * fallback, not explicit admin-configured rows.
	 */
	public function test_per_row_redirect_fires_when_global_toggle_off(): void {
		Settings::instance()->update(
			array_merge(
				Settings::instance()->all(),
				array( 'redirect_enabled' => false ),
			)
		);

		RedirectsModel::instance()->create(
			array(
				'source'        => '/test-redirect',
				'match_type'    => 'exact',
				'target_type'   => 'link',
				'target_url'    => 'https://example.com/destination',
				'redirect_type' => 301,
				'is_active'     => 1,
			)
		);

		$this->set_url( '/test-redirect' );

		$result = $this->run_redirect();
		$this->assertSame( 'https://example.com/destination', $result['url'] );
		$this->assertSame( 301, $result['status'] );
	}

	/**
	 * Inverse of the above: when `redirect_enabled` is off and there's
	 * no per-row match, the global fallback must NOT fire.
	 */
	public function test_global_fallback_skipped_when_toggle_off(): void {
		Settings::instance()->update(
			array_merge(
				Settings::instance()->all(),
				array(
					'redirect_enabled' => false,
					'redirect_target'  => 'link',
					'redirect_link'    => 'https://example.com/fallback',
				),
			)
		);

		$this->set_url( '/no-row-here' );

		$result = $this->run_redirect();
		$this->assertSame( '', $result['url'] );
	}

	/**
	 * Per-row redirects ignore the per-log `override_redirect` toggle.
	 * The redirect row's `is_active` is the master kill switch; the
	 * log override only modulates the global fallback.
	 */
	public function test_per_row_redirect_ignores_log_override_disable(): void {
		RedirectsModel::instance()->create(
			array(
				'source'      => '/per-row',
				'target_url'  => 'https://example.com/per-row',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$log_id = LogsModel::instance()->record_hit( array( 'url' => '/per-row' ) );
		LogsModel::instance()->set_overrides(
			$log_id,
			array( 'override_redirect' => LogsModel::OVERRIDE_DISABLE )
		);

		$this->enable_redirect();
		$this->set_url( '/per-row' );

		$result = $this->run_redirect();
		$this->assertSame( 'https://example.com/per-row', $result['url'] );
	}

	/**
	 * Log-level `override_redirect = DISABLE` silences the global
	 * fallback for that URL even when `redirect_enabled` is true.
	 */
	public function test_global_fallback_honors_log_override_disable(): void {
		$log_id = LogsModel::instance()->record_hit( array( 'url' => '/silenced' ) );
		LogsModel::instance()->set_overrides(
			$log_id,
			array( 'override_redirect' => LogsModel::OVERRIDE_DISABLE )
		);

		$this->enable_redirect(
			array(
				'redirect_target' => 'link',
				'redirect_link'   => 'https://example.com/fallback',
			)
		);
		$this->set_url( '/silenced' );

		$result = $this->run_redirect();
		$this->assertSame( '', $result['url'] );
	}

	/**
	 * Log-level `override_redirect = ENABLE` force-fires the global
	 * fallback for that URL even when `redirect_enabled` is false —
	 * the "this one URL I do want redirected" lever.
	 */
	public function test_global_fallback_force_fires_on_log_override_enable(): void {
		$log_id = LogsModel::instance()->record_hit( array( 'url' => '/forced' ) );
		LogsModel::instance()->set_overrides(
			$log_id,
			array( 'override_redirect' => LogsModel::OVERRIDE_ENABLE )
		);

		Settings::instance()->update(
			array_merge(
				Settings::instance()->all(),
				array(
					'redirect_enabled' => false,
					'redirect_target'  => 'link',
					'redirect_link'    => 'https://example.com/forced-target',
				),
			)
		);
		$this->set_url( '/forced' );

		$result = $this->run_redirect();
		$this->assertSame( 'https://example.com/forced-target', $result['url'] );
	}

	/**
	 * Linking a log to a redirect resets `override_redirect` to GLOBAL.
	 * Otherwise a stale DISABLE would silently re-apply if the admin
	 * later deletes the redirect row.
	 */
	public function test_link_redirect_resets_log_override(): void {
		$redirect_id = RedirectsModel::instance()->create(
			array(
				'source'      => '/reset-me',
				'target_url'  => 'https://example.com/reset-me',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$log_id = LogsModel::instance()->record_hit( array( 'url' => '/reset-me' ) );
		LogsModel::instance()->set_overrides(
			$log_id,
			array( 'override_redirect' => LogsModel::OVERRIDE_DISABLE )
		);

		LogsModel::instance()->link_redirect( $log_id, $redirect_id );

		$log = LogsModel::instance()->find( $log_id );
		$this->assertSame( LogsModel::OVERRIDE_GLOBAL, (int) $log->override_redirect );
	}
}
