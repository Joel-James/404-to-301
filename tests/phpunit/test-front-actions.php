<?php
/**
 * Tests for the front-end action chain: Redirect / Log / Email.
 *
 * Each action is invoked directly with a hand-built {@see Request}.
 * The Redirect action's `wp_safe_redirect() + exit` tail is short-
 * circuited by hooking `404_to_301_pre_redirect` and throwing — the
 * test then captures the would-be URL + status code from the hook.
 *
 * @package FourNotFour
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
}
