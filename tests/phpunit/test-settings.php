<?php
/**
 * Tests for {@see \DuckDev\FourNotFour\Settings}.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Settings;

/**
 * Class SettingsTest
 *
 * @group settings
 */
class SettingsTest extends WP_UnitTestCase {

	/**
	 * Drop both option keys so the next test starts on a clean slate.
	 */
	public function tear_down(): void {
		delete_option( Settings::KEY );
		delete_option( Settings::LEGACY_KEY );

		parent::tear_down();
	}


	/**
	 * `defaults()` returns a value for every documented setting key.
	 */
	public function test_defaults_provide_every_expected_key(): void {
		$defaults = Settings::instance()->defaults();

		foreach (
			array(
				'disable_guessing',
				'exclude_paths',
				'redirect_enabled',
				'redirect_type',
				'redirect_target',
				'redirect_link',
				'logs_enabled',
				'logs_skip_bots',
				'email_enabled',
				'email_recipient',
				'plugin_version',
				'db_version',
			) as $key
		) {
			$this->assertArrayHasKey( $key, $defaults, "Missing default: $key" );
		}
	}

	/**
	 * `set()` then `get()` round-trips through the option table.
	 */
	public function test_set_and_get_round_trip(): void {
		Settings::instance()->set( 'redirect_type', '302' );

		$this->assertSame( '302', Settings::instance()->get( 'redirect_type' ) );
	}

	/**
	 * Unknown redirect-type strings fall back to the default (`301`).
	 */
	public function test_sanitize_replaces_invalid_redirect_type_with_default(): void {
		$clean = Settings::instance()->sanitize( array( 'redirect_type' => 'banana' ) );

		$this->assertSame( '301', $clean['redirect_type'] );
	}

	/**
	 * Sanitizer coerces common truthy/falsy spellings into booleans.
	 */
	public function test_sanitize_coerces_boolean_inputs(): void {
		$clean = Settings::instance()->sanitize(
			array(
				'logs_enabled'  => 'true',
				'email_enabled' => '1',
				'mask_ip'       => 'no',
			)
		);

		$this->assertTrue( $clean['logs_enabled'] );
		$this->assertTrue( $clean['email_enabled'] );
		$this->assertFalse( $clean['mask_ip'] );
	}

	/**
	 * `disable_guessing` is a three-state enum (`off` / `light` /
	 * `strict`) and absorbs legacy boolean inputs.
	 */
	public function test_sanitize_disable_guessing_enum(): void {
		$cases = array(
			array( 'off', 'off' ),
			array( 'light', 'light' ),
			array( 'strict', 'strict' ),
			array( true, 'strict' ),     // Legacy boolean coercion.
			array( false, 'off' ),
			array( 'banana', 'light' ),  // Unknown → default.
		);

		foreach ( $cases as $case ) {
			[ $input, $expected ] = $case;
			$clean                = Settings::instance()->sanitize( array( 'disable_guessing' => $input ) );
			$this->assertSame( $expected, $clean['disable_guessing'], sprintf( 'Input %s should sanitise to %s.', var_export( $input, true ), $expected ) );
		}
	}

	/**
	 * `exclude_paths` is split on newlines, trimmed and deduplicated.
	 */
	public function test_sanitize_filters_exclude_paths_list(): void {
		$clean = Settings::instance()->sanitize(
			array( 'exclude_paths' => "/wp-content\n/foo\n/foo\n" )
		);

		$this->assertSame( array( '/wp-content', '/foo' ), $clean['exclude_paths'] );
	}

	/**
	 * Legacy v3 options migrate into the v4 shape on first read; subsequent calls are no-ops.
	 */
	public function test_legacy_option_migrates_once(): void {
		update_option(
			Settings::LEGACY_KEY,
			array(
				'redirect_type'        => '302',
				'redirect_to'          => 'link',
				'redirect_link'        => 'https://example.com/404',
				'redirect_log'         => '1',
				'email_notify'         => '1',
				'email_notify_address' => 'me@example.com',
				'disable_guessing'     => '1',
				'exclude_paths'        => "/wp-content\n/foo",
			)
		);

		Settings::instance()->maybe_migrate_legacy();

		$saved = get_option( Settings::KEY );

		$this->assertSame( '302', $saved['redirect_type'] );
		$this->assertSame( 'link', $saved['redirect_target'] );
		$this->assertSame( 'https://example.com/404', $saved['redirect_link'] );
		$this->assertTrue( $saved['logs_enabled'] );
		$this->assertTrue( $saved['email_enabled'] );
		$this->assertSame( array( 'me@example.com' ), $saved['email_recipient'] );
		$this->assertSame( 'strict', $saved['disable_guessing'] );
		$this->assertSame( array( '/wp-content', '/foo' ), $saved['exclude_paths'] );

		// Second call is a no-op.
		Settings::instance()->set( 'redirect_type', '307' );
		Settings::instance()->maybe_migrate_legacy();

		$this->assertSame( '307', Settings::instance()->get( 'redirect_type' ) );
	}

	/**
	 * Legacy `redirect_to = "0"` maps to the new `redirect_target = none` + disabled.
	 */
	public function test_legacy_redirect_to_zero_maps_to_none(): void {
		update_option(
			Settings::LEGACY_KEY,
			array( 'redirect_to' => '0' )
		);

		Settings::instance()->maybe_migrate_legacy();

		$saved = get_option( Settings::KEY );

		$this->assertSame( 'none', $saved['redirect_target'] );
		$this->assertFalse( $saved['redirect_enabled'] );
	}

	/* ---------------------------------------------------------------- *
	 * Settings update action hooks (#2)
	 * ---------------------------------------------------------------- */

	/**
	 * `404_to_301_settings_updated` fires after Settings::update() with
	 * the sanitised new payload and the previous snapshot.
	 */
	public function test_settings_updated_action_fires_on_update(): void {
		// Two writes: the first creates the option row (which routes
		// through `add_option_<KEY>`); the second is the genuine
		// update we want to assert on (`update_option_<KEY>`). We pick
		// a non-default value for the seed so `update_option()` doesn't
		// short-circuit on "value equals registered default" and skip
		// the write.
		Settings::instance()->update( array( 'redirect_type' => '302' ) );

		$captured = null;
		$listener = static function ( $new, $previous ) use ( &$captured ) {
			$captured = compact( 'new', 'previous' );
		};
		add_action( '404_to_301_settings_updated', $listener, 10, 2 );

		Settings::instance()->update( array( 'redirect_type' => '307' ) );

		remove_action( '404_to_301_settings_updated', $listener, 10 );

		$this->assertIsArray( $captured );
		$this->assertSame( '307', $captured['new']['redirect_type'] );
		$this->assertSame( '302', $captured['previous']['redirect_type'] );
	}

	/**
	 * The very first option write (which WP routes through
	 * `add_option()`) also fires the action — with an empty `$previous`
	 * so addons can tell it's the first save.
	 */
	public function test_settings_updated_action_fires_on_first_write(): void {
		delete_option( Settings::KEY );

		$captured = null;
		$listener = static function ( $new, $previous ) use ( &$captured ) {
			$captured = compact( 'new', 'previous' );
		};
		add_action( '404_to_301_settings_updated', $listener, 10, 2 );

		Settings::instance()->update( array( 'redirect_type' => '302' ) );

		remove_action( '404_to_301_settings_updated', $listener, 10 );

		$this->assertIsArray( $captured );
		$this->assertSame( '302', $captured['new']['redirect_type'] );
		$this->assertSame( array(), $captured['previous'] );
	}

	/**
	 * `404_to_301_settings_updated` also fires when the option is
	 * written through any non-Settings path (eg. REST's
	 * /wp/v2/settings bridge or a direct update_option call).
	 */
	public function test_settings_updated_action_fires_on_direct_update_option(): void {
		// Seed via the public path so the row exists with a non-default
		// `redirect_type` (`update_option` short-circuits when the
		// value equals the registered default).
		Settings::instance()->update( array( 'redirect_type' => '302' ) );

		$fired = false;
		$listener = static function () use ( &$fired ) {
			$fired = true;
		};
		add_action( '404_to_301_settings_updated', $listener );

		// Run the full settings array back through sanitize() so the
		// shape matches what `register_setting`'s sanitize_callback
		// would produce — that's what /wp/v2/settings emits too.
		$next = Settings::instance()->sanitize(
			array_merge( Settings::instance()->all(), array( 'redirect_type' => '307' ) )
		);
		update_option( Settings::KEY, $next );

		remove_action( '404_to_301_settings_updated', $listener );

		$this->assertTrue( $fired );
	}

	/**
	 * Per-key action fires exactly once per changed key — unchanged
	 * keys stay quiet.
	 */
	public function test_per_key_setting_updated_only_fires_for_changed_keys(): void {
		// Seed two non-default values so the option row exists and
		// both keys have a known "old" to diff against.
		Settings::instance()->update(
			array(
				'redirect_type'   => '302',
				'email_threshold' => 5,
			)
		);

		$changes = array();
		$listener = static function ( $new, $old ) use ( &$changes ) {
			$changes[] = compact( 'new', 'old' );
		};
		// Two listeners — one for the changed key, one for an unchanged key.
		add_action( '404_to_301_setting_updated_redirect_type', $listener, 10, 2 );

		$unchanged_fired = false;
		add_action(
			'404_to_301_setting_updated_email_threshold',
			static function () use ( &$unchanged_fired ) {
				$unchanged_fired = true;
			}
		);

		Settings::instance()->update(
			array(
				'redirect_type'   => '307',
				'email_threshold' => 5, // No change.
			)
		);

		remove_all_actions( '404_to_301_setting_updated_redirect_type' );
		remove_all_actions( '404_to_301_setting_updated_email_threshold' );

		$this->assertCount( 1, $changes, 'Per-key action should fire exactly once for the changed key.' );
		$this->assertSame( '307', $changes[0]['new'] );
		$this->assertSame( '302', $changes[0]['old'] );
		$this->assertFalse( $unchanged_fired, 'Unchanged keys must not fire their per-key action.' );
	}
}
