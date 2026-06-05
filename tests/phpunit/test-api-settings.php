<?php
/**
 * Tests for the `/settings/export` and `/settings/import` REST
 * endpoints (#9).
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Api\Settings as SettingsEndpoint;
use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Settings as SettingsStore;

/**
 * Class ApiSettingsTest
 *
 * @group api
 */
class ApiSettingsTest extends WP_UnitTestCase {

	const EXPORT_ROUTE = '/404-to-301/v1/settings/export';
	const IMPORT_ROUTE = '/404-to-301/v1/settings/import';

	/** @var int */
	private $admin_id = 0;

	public function set_up(): void {
		parent::set_up();
		Database::instance();
		$this->admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $this->admin_id );
		rest_get_server();
	}

	public function tear_down(): void {
		wp_set_current_user( 0 );
		delete_option( SettingsStore::KEY );
		remove_all_filters( '404_to_301_settings_export' );
		remove_all_filters( '404_to_301_settings_import' );
		parent::tear_down();
	}

	private function dispatch( string $method, string $route, array $params = array() ): WP_REST_Response {
		$request = new WP_REST_Request( $method, $route );
		foreach ( $params as $k => $v ) {
			$request->set_param( $k, $v );
		}
		return rest_get_server()->dispatch( $request );
	}

	/**
	 * Export returns a versioned envelope and strips install-state keys.
	 */
	public function test_export_returns_versioned_envelope_without_internal_keys(): void {
		// Seed something internal so we can verify it's stripped.
		SettingsStore::instance()->update(
			array_merge(
				SettingsStore::instance()->all(),
				array(
					'db_version'    => '4.0.0',
					'phase1_done'   => true,
					'redirect_type' => '302',
				)
			)
		);

		$response = $this->dispatch( 'GET', self::EXPORT_ROUTE );
		$this->assertSame( 200, $response->get_status() );

		$body = $response->get_data();
		$this->assertSame( '404-to-301', $body['plugin'] );
		$this->assertSame( SettingsEndpoint::ENVELOPE_VERSION, $body['schema_version'] );
		$this->assertArrayHasKey( 'exported_at', $body );
		$this->assertArrayHasKey( 'site_url', $body );
		$this->assertArrayHasKey( 'settings', $body );

		// User-facing keys present; install-state keys gone.
		$this->assertSame( '302', $body['settings']['redirect_type'] );
		foreach ( SettingsEndpoint::INTERNAL_KEYS as $internal ) {
			$this->assertArrayNotHasKey( $internal, $body['settings'], "Export should strip $internal" );
		}
	}

	/**
	 * Import accepts the raw `settings` object and applies it.
	 */
	public function test_import_accepts_raw_settings_object(): void {
		$response = $this->dispatch(
			'POST',
			self::IMPORT_ROUTE,
			array(
				'settings' => array( 'redirect_type' => '307' ),
			)
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 1, $response->get_data()['imported'] );
		$this->assertSame( '307', SettingsStore::instance()->get( 'redirect_type' ) );
	}

	/**
	 * Import also accepts the full envelope — auto-detected via the
	 * `plugin` key.
	 */
	public function test_import_accepts_full_envelope(): void {
		$envelope = array(
			'plugin'         => '404-to-301',
			'schema_version' => 1,
			'settings'       => array( 'redirect_type' => '302' ),
		);

		$response = $this->dispatch(
			'POST',
			self::IMPORT_ROUTE,
			array( 'settings' => $envelope )
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( '302', SettingsStore::instance()->get( 'redirect_type' ) );
	}

	/**
	 * Install-state keys in an incoming payload are stripped — an
	 * imported envelope must not clobber the destination's db_version
	 * or migration flags.
	 */
	public function test_import_ignores_internal_keys(): void {
		// Pin the local installer-state key to a known value.
		SettingsStore::instance()->update(
			array_merge(
				SettingsStore::instance()->all(),
				array( 'db_version' => 'local-value' )
			)
		);

		$response = $this->dispatch(
			'POST',
			self::IMPORT_ROUTE,
			array(
				'settings' => array(
					'redirect_type' => '301',
					'db_version'    => 'imported-value',
					'phase1_done'   => true,
				),
			)
		);

		$this->assertSame( 200, $response->get_status() );
		// db_version stays on the local value; imported one is ignored.
		$this->assertSame( 'local-value', SettingsStore::instance()->get( 'db_version' ) );
	}

	/**
	 * Empty payloads (after internal-key stripping) are a 400, not a
	 * silent no-op.
	 */
	public function test_import_rejects_payload_after_stripping_becomes_empty(): void {
		$response = $this->dispatch(
			'POST',
			self::IMPORT_ROUTE,
			array(
				'settings' => array( 'db_version' => 'x' ),
			)
		);

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_empty_payload', $response->get_data()['code'] );
	}

	/**
	 * `404_to_301_settings_export` runs before the response is sent so
	 * addons can extend the envelope.
	 */
	public function test_export_filter_can_extend_envelope(): void {
		add_filter(
			'404_to_301_settings_export',
			static function ( $envelope ) {
				$envelope['my_addon'] = array( 'version' => '1.0' );
				return $envelope;
			}
		);

		$response = $this->dispatch( 'GET', self::EXPORT_ROUTE );
		$this->assertSame( '1.0', $response->get_data()['my_addon']['version'] );
	}

	/**
	 * `404_to_301_settings_import` runs before the merge so addons can
	 * transform incoming payloads.
	 */
	public function test_import_filter_can_transform_payload(): void {
		add_filter(
			'404_to_301_settings_import',
			static function ( $incoming ) {
				$incoming['redirect_type'] = '307';
				return $incoming;
			}
		);

		$this->dispatch(
			'POST',
			self::IMPORT_ROUTE,
			array(
				'settings' => array( 'redirect_type' => '301' ),
			)
		);

		$this->assertSame( '307', SettingsStore::instance()->get( 'redirect_type' ) );
	}
}
