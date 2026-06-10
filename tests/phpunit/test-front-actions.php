<?php
/**
 * Tests for the front-end action chain: Redirect / Log / Email.
 *
 * Each action is invoked directly with a hand-built {@see Request}.
 * The Redirect action's `wp_safe_redirect() + exit` tail is short-
 * circuited by hooking `404_to_301_pre_redirect` and throwing — the
 * test then captures the would-be URL + status code from the hook.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Front\Actions\Email;
use DuckDev\FourNotFour\Front\Actions\Log;
use DuckDev\FourNotFour\Front\Actions\Redirect;
use DuckDev\FourNotFour\Front\Request;
use DuckDev\FourNotFour\Models\Logs as LogsModel;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;
use DuckDev\FourNotFour\Settings;

/**
 * Thrown from the `404_to_301_pre_redirect` hook so the test can
 * assert on the resolved redirect without letting the action call
 * `wp_safe_redirect() + exit` and abort PHPUnit.
 */
class FrontActionsRedirectFired extends \RuntimeException {

	/** @var string */
	public $url = '';

	/** @var int */
	public $status = 0;
}

/**
 * Class FrontActionsTest
 *
 * @group front
 */
class FrontActionsTest extends WP_UnitTestCase {

	/**
	 * Captured `wp_mail` payloads keyed by call order.
	 *
	 * @var array<int, array{to:mixed,subject:string,message:string}>
	 */
	private $mails = array();

	public function set_up(): void {
		parent::set_up();

		Database::instance();
		$this->mails = array();

		// Force `is_404()` to true for every Request without booting the
		// full WP query — the actions only consult the filter.
		add_filter( '404_to_301_request_is_404', '__return_true' );

		// Capture `wp_mail` calls without ever shelling out to PHPMailer.
		add_filter(
			'pre_wp_mail',
			function ( $short_circuit, $atts ) {
				$this->mails[] = array(
					'to'      => $atts['to'] ?? '',
					'subject' => (string) ( $atts['subject'] ?? '' ),
					'message' => (string) ( $atts['message'] ?? '' ),
				);
				return true;
			},
			10,
			2
		);

		// Short-circuit `wp_safe_redirect` so the headers + `exit` in
		// the Redirect action never run. Hooking `wp_redirect` (the
		// filter `wp_safe_redirect` ultimately calls) means the row's
		// `record_hit` + log-linking writes — which happen before
		// `wp_safe_redirect` — still execute.
		add_filter(
			'wp_redirect',
			function ( $location, $status ) {
				$ex         = new FrontActionsRedirectFired( 'redirect fired' );
				$ex->url    = (string) $location;
				$ex->status = (int) $status;
				throw $ex;
			},
			10,
			2
		);

		// `wp_safe_redirect` rejects off-site hosts; allow example.com
		// so the captured URL on the filter is the one the action picked.
		add_filter(
			'allowed_redirect_hosts',
			static function ( $hosts ) {
				$hosts[] = 'example.com';
				return $hosts;
			}
		);

		// REQUEST_URI is what `Request::url()` reads from.
		$_SERVER['REQUEST_URI']    = '/missing-page';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11) Gecko/20100101 Firefox/120.0';
	}

	public function tear_down(): void {
		remove_all_filters( '404_to_301_request_is_404' );
		remove_all_filters( 'pre_wp_mail' );
		remove_all_filters( 'wp_redirect' );
		remove_all_filters( 'allowed_redirect_hosts' );

		// Don't unset `$_SERVER['REQUEST_URI']` etc. — `wp-cron` reads
		// from it on later teardown steps and emits a deprecation when
		// the key is missing.

		parent::tear_down();
	}

	/**
	 * Apply a settings overlay on top of the defaults.
	 */
	private function configure( array $overrides ): void {
		$current = Settings::instance()->all();
		Settings::instance()->update( array_merge( $current, $overrides ) );
	}

	/**
	 * All three actions enabled, a 404 is logged, an email goes out
	 * and a redirect is resolved against the configured global link.
	 */
	public function test_404_with_redirect_log_email_all_enabled(): void {
		$this->configure(
			array(
				'logs_enabled'    => true,
				'email_enabled'   => true,
				'email_recipient' => 'admin@example.com',
				'email_threshold' => 1,
				'redirect_enabled' => true,
				'redirect_target' => 'link',
				'redirect_link'   => 'https://example.com/fallback',
				'redirect_type'   => '301',
			)
		);

		$request = new Request();

		( new Log() )->run( $request );
		( new Email() )->run( $request );

		// Log row should exist with one hit.
		$log = LogsModel::instance()->get_by_url( '/missing-page' );
		$this->assertNotNull( $log );
		$this->assertSame( 1, (int) $log->hits );

		// Email should have fired exactly once.
		$this->assertCount( 1, $this->mails );
		$this->assertSame( array( 'admin@example.com' ), $this->mails[0]['to'] );

		// Redirect action throws the captured-redirect exception.
		$thrown = null;
		try {
			( new Redirect() )->run( $request );
		} catch ( FrontActionsRedirectFired $e ) {
			$thrown = $e;
		}
		$this->assertNotNull( $thrown );
		$this->assertSame( 'https://example.com/fallback', $thrown->url );
		$this->assertSame( 301, $thrown->status );
	}

	/**
	 * Everything off — the chain is a complete no-op.
	 */
	public function test_404_with_all_disabled_is_a_no_op(): void {
		$this->configure(
			array(
				'logs_enabled'     => false,
				'email_enabled'    => false,
				'redirect_enabled' => false,
			)
		);

		$request = new Request();

		( new Log() )->run( $request );
		( new Email() )->run( $request );
		( new Redirect() )->run( $request );

		$this->assertNull( LogsModel::instance()->get_by_url( '/missing-page' ) );
		$this->assertSame( array(), $this->mails );
	}

	/**
	 * Only logging is enabled — log written, no email, no redirect.
	 */
	public function test_404_with_only_log_enabled(): void {
		$this->configure(
			array(
				'logs_enabled'     => true,
				'email_enabled'    => false,
				'redirect_enabled' => false,
			)
		);

		$request = new Request();

		( new Log() )->run( $request );
		( new Email() )->run( $request );

		// Redirect action is disabled and must NOT throw.
		( new Redirect() )->run( $request );

		$this->assertNotNull( LogsModel::instance()->get_by_url( '/missing-page' ) );
		$this->assertSame( array(), $this->mails );
	}

	/**
	 * Email-only with a threshold > current hit count: no email goes out.
	 */
	public function test_email_threshold_blocks_first_hit(): void {
		$this->configure(
			array(
				'logs_enabled'     => true,
				'email_enabled'    => true,
				'email_recipient'  => 'admin@example.com',
				'email_threshold'  => 3,
				'redirect_enabled' => false,
			)
		);

		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );

		$this->assertSame( array(), $this->mails );

		// Two more hits to land exactly on the threshold.
		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );
		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );

		$this->assertCount( 1, $this->mails, 'Email should fire on the exact threshold hit.' );

		// And a fourth hit — past the threshold — does NOT re-send.
		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );
		$this->assertCount( 1, $this->mails );
	}

	/**
	 * Email recipient must be a valid email — bad config silently
	 * disables the action without throwing.
	 */
	public function test_email_skips_when_recipient_invalid(): void {
		$this->configure(
			array(
				'logs_enabled'    => true,
				'email_enabled'   => true,
				'email_recipient' => 'not-an-email',
				'email_threshold' => 1,
			)
		);

		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );

		$this->assertSame( array(), $this->mails );
	}

	/**
	 * Excluded URL paths short-circuit every action.
	 */
	public function test_excluded_path_short_circuits_every_action(): void {
		$this->configure(
			array(
				'logs_enabled'     => true,
				'email_enabled'    => true,
				'email_recipient'  => 'admin@example.com',
				'email_threshold'  => 1,
				'redirect_enabled' => true,
				'redirect_target'  => 'link',
				'redirect_link'    => 'https://example.com/fallback',
				'exclude_paths'    => array( '/missing' ),
			)
		);

		$request = new Request();

		// Excluded path must NOT cause a redirect exception.
		( new Redirect() )->run( $request );
		( new Log() )->run( $request );
		( new Email() )->run( $request );

		$this->assertNull( LogsModel::instance()->get_by_url( '/missing-page' ) );
		$this->assertSame( array(), $this->mails );
	}

	/**
	 * Per-row redirect wins over the global default and uses the
	 * row's own `redirect_type`.
	 */
	public function test_per_row_redirect_wins_over_global_default(): void {
		$this->configure(
			array(
				'redirect_enabled' => true,
				'redirect_target'  => 'link',
				'redirect_link'    => 'https://example.com/global',
				'redirect_type'    => '301',
			)
		);

		RedirectsModel::instance()->create(
			array(
				'source'        => '/missing-page',
				'target_url'    => 'https://example.com/per-row',
				'target_type'   => 'link',
				'match_type'    => 'exact',
				'redirect_type' => 302,
				'is_active'     => 1,
			)
		);

		$thrown = null;
		try {
			( new Redirect() )->run( new Request() );
		} catch ( FrontActionsRedirectFired $e ) {
			$thrown = $e;
		}

		$this->assertNotNull( $thrown );
		$this->assertSame( 'https://example.com/per-row', $thrown->url );
		$this->assertSame( 302, $thrown->status );
	}

	/**
	 * Global redirect with `target = none` aborts before sending headers.
	 */
	public function test_global_target_none_does_not_redirect(): void {
		$this->configure(
			array(
				'redirect_enabled' => true,
				'redirect_target'  => 'none',
			)
		);

		// `run()` must NOT throw — `target_type = none` resolves to empty.
		( new Redirect() )->run( new Request() );

		$this->addToAssertionCount( 1 );
	}

	/* ---------------------------------------------------------------- *
	 * Per-redirect query handling — `preserve` (#6)
	 * ---------------------------------------------------------------- */

	/**
	 * `preserve` rows forward the request query string to the
	 * destination, with destination keys winning on collision.
	 */
	public function test_preserve_mode_forwards_query_to_destination(): void {
		$model = RedirectsModel::instance();
		$model->create(
			array(
				'source'         => '/landing',
				'target_url'     => 'https://example.com/dest?source=campaign',
				'target_type'    => 'link',
				'match_type'     => 'exact',
				'query_handling' => 'preserve',
				'is_active'      => 1,
			)
		);

		$this->configure( array( 'redirect_enabled' => true ) );

		$_SERVER['REQUEST_URI'] = '/landing?utm_source=newsletter&source=user';

		$thrown = null;
		try {
			( new Redirect() )->run( new Request() );
		} catch ( FrontActionsRedirectFired $e ) {
			$thrown = $e;
		}

		$this->assertNotNull( $thrown );

		$parts = wp_parse_url( $thrown->url );
		$query = array();
		wp_parse_str( $parts['query'] ?? '', $query );

		$this->assertSame( 'newsletter', $query['utm_source'] ?? null );
		// Destination's explicit `source=campaign` wins over the
		// incoming `source=user`.
		$this->assertSame( 'campaign', $query['source'] ?? null );
	}

	/* ---------------------------------------------------------------- *
	 * disable_guessing enum (#14)
	 * ---------------------------------------------------------------- */

	/**
	 * `off` mode leaves both canonical filters alone.
	 */
	public function test_disable_guessing_off_leaves_filters_untouched(): void {
		$this->configure( array( 'disable_guessing' => 'off' ) );

		$controller = \DuckDev\FourNotFour\Front\Controller::instance();

		$this->assertSame( '/wherever', $controller->disable_canonical_guessing( '/wherever' ) );
		$this->assertTrue( $controller->maybe_block_404_guess( true ) );
	}

	/**
	 * `light` mode blocks the closest-post guess but lets the rest of
	 * `redirect_canonical()` run.
	 */
	public function test_disable_guessing_light_blocks_only_404_guess(): void {
		$this->configure( array( 'disable_guessing' => 'light' ) );

		$controller = \DuckDev\FourNotFour\Front\Controller::instance();

		// `redirect_canonical` passes through (trailing slash etc. still
		// happens).
		$this->assertSame( '/wherever', $controller->disable_canonical_guessing( '/wherever' ) );
		// The targeted 404-guess filter is shorted to false.
		$this->assertFalse( $controller->maybe_block_404_guess( true ) );
	}

	/**
	 * `strict` mode returns false from `redirect_canonical` too — full
	 * bypass of WordPress URL canonicalisation.
	 */
	public function test_disable_guessing_strict_kills_redirect_canonical(): void {
		$this->configure( array( 'disable_guessing' => 'strict' ) );

		$controller = \DuckDev\FourNotFour\Front\Controller::instance();

		$this->assertFalse( $controller->disable_canonical_guessing( '/wherever' ) );
		$this->assertFalse( $controller->maybe_block_404_guess( true ) );
	}

	/**
	 * `?p=` short-circuit keeps direct post-id links working in every
	 * mode — even strict.
	 */
	public function test_disable_guessing_preserves_post_id_shortlink(): void {
		$this->configure( array( 'disable_guessing' => 'strict' ) );

		$controller = \DuckDev\FourNotFour\Front\Controller::instance();

		$_GET['p'] = '42';
		try {
			$this->assertSame( '/some-target', $controller->disable_canonical_guessing( '/some-target' ) );
			$this->assertTrue( $controller->maybe_block_404_guess( true ) );
		} finally {
			unset( $_GET['p'] );
		}
	}

	/**
	 * Standalone custom redirects (no existing log row) consume the
	 * request: the URL is treated as routed, so we don't record it as
	 * a 404 in the logs.
	 */
	public function test_log_skipped_when_standalone_redirect_matches(): void {
		$this->configure( array( 'logs_enabled' => true ) );

		RedirectsModel::instance()->create(
			array(
				'source'      => '/missing-page',
				'target_url'  => 'https://example.com/routed',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		( new Log() )->run( new Request() );

		$this->assertNull( LogsModel::instance()->get_by_url( '/missing-page' ) );
	}

	/**
	 * A pre-existing log row means the URL is already being tracked,
	 * so a matching redirect should NOT suppress the hit-counter bump
	 * (admins use Logs to triage; the count is the triage signal).
	 */
	public function test_log_still_bumps_when_linked_redirect_matches(): void {
		$this->configure( array( 'logs_enabled' => true ) );

		LogsModel::instance()->record_hit( array( 'url' => '/missing-page' ) );

		RedirectsModel::instance()->create(
			array(
				'source'      => '/missing-page',
				'target_url'  => 'https://example.com/routed',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		( new Log() )->run( new Request() );

		$log = LogsModel::instance()->get_by_url( '/missing-page' );
		$this->assertNotNull( $log );
		$this->assertSame( 2, (int) $log->hits );
	}

	/**
	 * Email skips when a standalone custom redirect handles the URL —
	 * a routed URL isn't a broken-link signal worth alerting on.
	 */
	public function test_email_skipped_when_standalone_redirect_matches(): void {
		$this->configure(
			array(
				'logs_enabled'    => true,
				'email_enabled'   => true,
				'email_recipient' => 'admin@example.com',
				'email_threshold' => 1,
			)
		);

		RedirectsModel::instance()->create(
			array(
				'source'      => '/missing-page',
				'target_url'  => 'https://example.com/routed',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );

		$this->assertSame( array(), $this->mails );
	}

	/**
	 * `override_email = DISABLE` silences the alert for this URL even
	 * when the global `email_enabled` toggle is on.
	 */
	public function test_email_honors_log_override_disable(): void {
		$this->configure(
			array(
				'logs_enabled'    => true,
				'email_enabled'   => true,
				'email_recipient' => 'admin@example.com',
				'email_threshold' => 1,
			)
		);

		$id = LogsModel::instance()->record_hit( array( 'url' => '/missing-page' ) );
		LogsModel::instance()->set_overrides(
			$id,
			array( 'override_email' => LogsModel::OVERRIDE_DISABLE )
		);

		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );

		$this->assertSame( array(), $this->mails );
	}

	/**
	 * `override_email = ENABLE` force-sends the alert even when the
	 * global `email_enabled` toggle is off.
	 */
	public function test_email_honors_log_override_enable_when_global_off(): void {
		$this->configure(
			array(
				'logs_enabled'    => true,
				'email_enabled'   => false,
				'email_recipient' => 'admin@example.com',
				'email_threshold' => 1,
			)
		);

		$id = LogsModel::instance()->record_hit( array( 'url' => '/missing-page' ) );
		LogsModel::instance()->set_overrides(
			$id,
			array( 'override_email' => LogsModel::OVERRIDE_ENABLE )
		);

		( new Log() )->run( $request = new Request() );
		( new Email() )->run( $request );

		$this->assertCount( 1, $this->mails );
	}
}
