<?php
/**
 * Tests for {@see \DuckDev\FourNotFour\Freemius}.
 *
 * The Freemius SDK ultimately talks to a remote API, so these tests
 * focus on the wrapper logic (registered-addons filter, license-item
 * shape, error paths for unregistered ids) and seed the SDK's local
 * activation option directly. Nothing hits the network.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\Freemius\Services\Service;
use DuckDev\FourNotFour\Freemius;

/**
 * Class LicenseTest
 *
 * @group license
 */
class LicenseTest extends WP_UnitTestCase {

	/**
	 * SDK option key the License service reads / writes.
	 */
	const ACTIVATION_OPTION = 'duckdev_freemius_activation_data';

	/**
	 * Dummy addon id used across the suite (deliberately outside the
	 * range of any real Freemius project so we can't collide).
	 */
	const ADDON_ID = 999001;

	public function tear_down(): void {
		remove_all_filters( '404_to_301_register_addon' );
		delete_option( self::ACTIVATION_OPTION );

		parent::tear_down();
	}

	/**
	 * Register a dummy addon via the documented filter.
	 */
	private function register_dummy_addon( int $id = self::ADDON_ID ): void {
		add_filter(
			'404_to_301_register_addon',
			static function ( $addons ) use ( $id ) {
				$addons[ $id ] = array(
					'slug'       => 'dummy-addon-' . $id,
					'main_file'  => 'dummy-addon-' . $id . '/dummy-addon.php',
					'public_key' => 'pk_dummy_' . $id,
					'is_premium' => true,
					'has_addons' => false,
				);
				return $addons;
			}
		);
	}

	/**
	 * Pretend the SDK has activated a license for an addon by writing
	 * directly to its option (the License service is a thin wrapper
	 * around this option).
	 */
	private function seed_activation( int $id, string $key, string $status = Service::ACTIVATED ): void {
		$option        = get_option( self::ACTIVATION_OPTION, array() );
		$option[ $id ] = array(
			'status'            => $status,
			'activation_params' => array(
				'license_key' => $key,
			),
		);
		update_option( self::ACTIVATION_OPTION, $option );
	}

	/**
	 * Addons registered via the documented `404_to_301_register_addon`
	 * filter show up in the wrapper's list keyed by Freemius id.
	 */
	public function test_get_registered_addons_returns_filtered_list(): void {
		$this->register_dummy_addon();

		$registered = Freemius::instance()->get_registered_addons();

		$this->assertArrayHasKey( self::ADDON_ID, $registered );
		$this->assertSame( 'dummy-addon-' . self::ADDON_ID, $registered[ self::ADDON_ID ]['slug'] );
	}

	/**
	 * Without any addons hooking the filter, the list is empty —
	 * never `null` or `false`, so callers can safely `foreach` it.
	 */
	public function test_get_registered_addons_is_empty_by_default(): void {
		$this->assertSame( array(), Freemius::instance()->get_registered_addons() );
	}

	/**
	 * A seeded `activated` activation surfaces with `active = true`
	 * and the license key copied through verbatim.
	 */
	public function test_get_license_items_reports_active_addon(): void {
		$this->register_dummy_addon();
		$this->seed_activation( self::ADDON_ID, 'ABCD-EFGH-IJKL-MNOP' );

		$items = Freemius::instance()->get_license_items();

		$this->assertArrayHasKey( self::ADDON_ID, $items );
		$this->assertSame( 'ABCD-EFGH-IJKL-MNOP', $items[ self::ADDON_ID ]['key'] );
		$this->assertSame( Service::ACTIVATED, $items[ self::ADDON_ID ]['status'] );
		$this->assertTrue( $items[ self::ADDON_ID ]['active'] );
	}

	/**
	 * A seeded `deactivated` activation keeps the stored key but flips
	 * `active` to false so the UI can render the right badge.
	 */
	public function test_get_license_items_reports_deactivated_addon(): void {
		$this->register_dummy_addon();
		$this->seed_activation( self::ADDON_ID, 'KEY-1', Service::DEACTIVATED );

		$items = Freemius::instance()->get_license_items();

		$this->assertSame( Service::DEACTIVATED, $items[ self::ADDON_ID ]['status'] );
		$this->assertFalse( $items[ self::ADDON_ID ]['active'] );
		$this->assertSame( 'KEY-1', $items[ self::ADDON_ID ]['key'] );
	}

	/**
	 * A registered-but-never-activated addon still appears in the list,
	 * with empty `key` / `status` and `active = false`.
	 */
	public function test_get_license_items_returns_empty_when_addon_not_seeded(): void {
		$this->register_dummy_addon();

		$items = Freemius::instance()->get_license_items();

		$this->assertArrayHasKey( self::ADDON_ID, $items );
		$this->assertSame( '', $items[ self::ADDON_ID ]['key'] );
		$this->assertSame( '', $items[ self::ADDON_ID ]['status'] );
		$this->assertFalse( $items[ self::ADDON_ID ]['active'] );
	}

	/**
	 * Activation against an id the filter never registered returns
	 * `WP_Error('addon_not_registered')` instead of hitting the API.
	 */
	public function test_activate_license_rejects_unregistered_addon(): void {
		$result = Freemius::instance()->activate_license( 424242, 'SOMEKEY' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'addon_not_registered', $result->get_error_code() );
	}

	/**
	 * Deactivation mirrors activation: unregistered ids fail closed
	 * with `WP_Error('addon_not_registered')`.
	 */
	public function test_deactivate_license_rejects_unregistered_addon(): void {
		$result = Freemius::instance()->deactivate_license( 424242 );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'addon_not_registered', $result->get_error_code() );
	}

	/**
	 * `for_addon()` refuses to build a client for a non-positive id —
	 * a sanity guard around the SDK's own id assumption.
	 */
	public function test_for_addon_rejects_invalid_id(): void {
		$this->assertNull( Freemius::instance()->for_addon( 0, array() ) );
		$this->assertNull( Freemius::instance()->for_addon( -1, array() ) );
	}

	/**
	 * Two calls to `for_addon()` with the same id return the same
	 * client instance — important so the SDK's own per-id cache and
	 * the wrapper's cache stay in sync.
	 */
	public function test_for_addon_memoises_client_per_id(): void {
		$this->register_dummy_addon();

		$first  = Freemius::instance()->for_addon(
			self::ADDON_ID,
			array(
				'slug'       => 'dummy-addon-' . self::ADDON_ID,
				'public_key' => 'pk_dummy',
				'is_premium' => true,
			)
		);
		$second = Freemius::instance()->for_addon(
			self::ADDON_ID,
			array(
				'slug'       => 'dummy-addon-' . self::ADDON_ID,
				'public_key' => 'pk_dummy',
				'is_premium' => true,
			)
		);

		$this->assertNotNull( $first );
		$this->assertSame( $first, $second );
	}

	/**
	 * The SDK class is shipped via composer, so the wrapper should
	 * always report `is_ready() = true` inside the test suite.
	 */
	public function test_is_ready_when_sdk_class_present(): void {
		$this->assertTrue( Freemius::instance()->is_ready() );
	}
}
