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
				'logs_enabled'     => 'true',
				'email_enabled'    => '1',
				'mask_ip'          => 'no',
				'disable_guessing' => 'off',
			)
		);

		$this->assertTrue( $clean['logs_enabled'] );
		$this->assertTrue( $clean['email_enabled'] );
		$this->assertFalse( $clean['mask_ip'] );
		$this->assertFalse( $clean['disable_guessing'] );
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
		$this->assertTrue( $saved['disable_guessing'] );
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
}
