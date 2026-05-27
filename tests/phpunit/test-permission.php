<?php
/**
 * Tests for {@see \DuckDev\FourNotFour\Utils\Permission}.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Utils\Permission;

/**
 * Class PermissionTest
 *
 * @group permission
 */
class PermissionTest extends WP_UnitTestCase {

	public function tear_down(): void {
		remove_all_filters( '404_to_301_capability' );
		remove_all_filters( '404_to_301_has_access' );
		wp_set_current_user( 0 );

		parent::tear_down();
	}

	public function test_default_cap_is_manage_options(): void {
		$this->assertSame( 'manage_options', Permission::get_cap() );
	}

	public function test_cap_filter_takes_precedence(): void {
		add_filter(
			'404_to_301_capability',
			static function () {
				return 'edit_posts';
			}
		);

		$this->assertSame( 'edit_posts', Permission::get_cap() );
	}

	public function test_admin_user_passes_access_check(): void {
		$admin = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin );

		$this->assertTrue( Permission::has_access() );
	}

	public function test_subscriber_fails_access_check(): void {
		$user = $this->factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user );

		$this->assertFalse( Permission::has_access() );
	}

	public function test_has_access_filter_can_override(): void {
		$user = $this->factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user );

		add_filter( '404_to_301_has_access', '__return_true' );

		$this->assertTrue( Permission::has_access() );
	}
}
