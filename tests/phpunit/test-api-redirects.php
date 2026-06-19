<?php
/**
 * REST tests for {@see \DuckDev\FourNotFour\Api\Redirects}.
 *
 * Exercises the public surface of `/404-to-301/v1/redirects*` end-to-end —
 * routes are dispatched through {@see rest_get_server()} so the permission
 * callback, arg validation and response shaping all run.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;

/**
 * Class ApiRedirectsTest
 *
 * @group api
 */
class ApiRedirectsTest extends WP_UnitTestCase {

	/**
	 * REST namespace under test.
	 */
	const ROUTE = '/404-to-301/v1/redirects';

	/**
	 * Admin user id, used to satisfy the `manage_options` permission
	 * callback.
	 *
	 * @var int
	 */
	private $admin_id = 0;

	/**
	 * Boot the tables, log in as an admin, and force the REST server
	 * to register every route before each test runs.
	 */
	public function set_up(): void {
		parent::set_up();

		Database::instance();

		$this->admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $this->admin_id );

		rest_get_server();
	}

	/**
	 * Reset the current user so the next test starts logged out.
	 */
	public function tear_down(): void {
		wp_set_current_user( 0 );

		parent::tear_down();
	}

	/**
	 * Dispatch a request through the REST server and return the response.
	 *
	 * @param string $method HTTP method.
	 * @param string $route  Route path.
	 * @param array  $params Body / query params.
	 *
	 * @return WP_REST_Response
	 */
	private function dispatch( string $method, string $route, array $params = array() ): WP_REST_Response {
		$request = new WP_REST_Request( $method, $route );

		foreach ( $params as $key => $value ) {
			$request->set_param( $key, $value );
		}

		return rest_get_server()->dispatch( $request );
	}

	/**
	 * `POST /redirects` returns 201 and persists the new row.
	 */
	public function test_create_returns_201_and_persists_row(): void {
		$response = $this->dispatch(
			'POST',
			self::ROUTE,
			array(
				'source'        => '/old',
				'target_url'    => 'https://example.com/new',
				'match_type'    => 'exact',
				'target_type'   => 'link',
				'redirect_type' => 302,
				'is_active'     => true,
			)
		);

		$this->assertSame( 201, $response->get_status() );
		$body = $response->get_data();
		$this->assertGreaterThan( 0, $body['id'] );
		$this->assertSame( '/old', $body['source'] );
		$this->assertSame( 302, $body['redirect_type'] );
		$this->assertTrue( $body['is_active'] );

		// And the row really is on disk.
		$this->assertNotNull( RedirectsModel::instance()->find( $body['id'] ) );
	}

	/**
	 * Create requests without a `source` are rejected before they reach the callback.
	 */
	public function test_create_requires_source(): void {
		$response = $this->dispatch(
			'POST',
			self::ROUTE,
			array( 'target_url' => 'https://example.com/' )
		);

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_missing_callback_param', $response->get_data()['code'] );
	}

	/**
	 * REST's `enum` validator rejects unsupported redirect types with 400.
	 */
	public function test_create_rejects_out_of_enum_redirect_type(): void {
		$response = $this->dispatch(
			'POST',
			self::ROUTE,
			array(
				'source'        => '/bad-type',
				'redirect_type' => 418,
			)
		);

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_invalid_param', $response->get_data()['code'] );
	}

	/**
	 * `GET /redirects/{id}` returns the shaped row for an existing redirect.
	 */
	public function test_get_returns_shaped_row(): void {
		$id = RedirectsModel::instance()->create(
			array(
				'source'      => '/get-it',
				'target_url'  => 'https://example.com/x',
				'match_type'  => 'exact',
				'target_type' => 'link',
				'is_active'   => 1,
			)
		);

		$response = $this->dispatch( 'GET', self::ROUTE . '/' . $id );

		$this->assertSame( 200, $response->get_status() );
		$body = $response->get_data();
		$this->assertSame( $id, $body['id'] );
		$this->assertSame( '/get-it', $body['source'] );
	}

	/**
	 * `GET /redirects/{id}` returns 404 when the row doesn't exist.
	 */
	public function test_get_returns_404_for_missing_row(): void {
		$response = $this->dispatch( 'GET', self::ROUTE . '/999999' );

		$this->assertSame( 404, $response->get_status() );
	}

	/**
	 * `PATCH /redirects/{id}` updates the row and refreshes the `source_hash` when `source` changes.
	 */
	public function test_update_modifies_row_and_refreshes_hash(): void {
		$id = RedirectsModel::instance()->create(
			array(
				'source'      => '/before',
				'target_url'  => 'https://example.com/before',
				'match_type'  => 'exact',
				'target_type' => 'link',
				'is_active'   => 1,
			)
		);

		$response = $this->dispatch(
			'PATCH',
			self::ROUTE . '/' . $id,
			array(
				'source'     => '/after',
				'target_url' => 'https://example.com/after',
				'is_active'  => false,
			)
		);

		$this->assertSame( 200, $response->get_status() );
		$body = $response->get_data();
		$this->assertSame( '/after', $body['source'] );
		$this->assertSame( 'https://example.com/after', $body['target_url'] );
		$this->assertFalse( $body['is_active'] );

		// `find_exact` runs against `source_hash`, so we can confirm the
		// hash was refreshed during update.
		$this->assertNull( RedirectsModel::instance()->find_exact( '/before' ) );
		// `find_exact` only returns active rows — re-enable and re-check.
		RedirectsModel::instance()->update( $id, array( 'is_active' => 1 ) );
		$this->assertNotNull( RedirectsModel::instance()->find_exact( '/after' ) );
	}

	/**
	 * `PATCH /redirects/{id}` returns 404 when the row doesn't exist.
	 */
	public function test_update_returns_404_for_missing_row(): void {
		$response = $this->dispatch( 'PATCH', self::ROUTE . '/999999', array( 'is_active' => true ) );

		$this->assertSame( 404, $response->get_status() );
	}

	/**
	 * `DELETE /redirects/{id}` removes the row.
	 */
	public function test_delete_removes_row(): void {
		$id = RedirectsModel::instance()->create(
			array(
				'source'      => '/del',
				'target_url'  => 'https://example.com/',
				'match_type'  => 'exact',
				'target_type' => 'link',
				'is_active'   => 1,
			)
		);

		$response = $this->dispatch( 'DELETE', self::ROUTE . '/' . $id );

		$this->assertSame( 200, $response->get_status() );
		$this->assertTrue( $response->get_data()['deleted'] );
		$this->assertNull( RedirectsModel::instance()->find( $id ) );
	}

	/**
	 * `DELETE /redirects` with an `ids` payload removes every listed row.
	 */
	public function test_bulk_delete_removes_every_listed_row(): void {
		$model = RedirectsModel::instance();
		$ids   = array();
		foreach ( array( '/b1', '/b2', '/b3' ) as $source ) {
			$ids[] = $model->create(
				array(
					'source'      => $source,
					'target_url'  => 'https://example.com/' . ltrim( $source, '/' ),
					'match_type'  => 'exact',
					'target_type' => 'link',
					'is_active'   => 1,
				)
			);
		}

		$response = $this->dispatch( 'DELETE', self::ROUTE, array( 'ids' => $ids ) );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 3, $response->get_data()['deleted'] );
	}

	/**
	 * `GET /redirects?is_active=…` narrows the collection to matching rows.
	 */
	public function test_list_filters_by_is_active(): void {
		$model = RedirectsModel::instance();
		$model->create(
			array(
				'source'      => '/on',
				'target_url'  => 'https://example.com/on',
				'match_type'  => 'exact',
				'target_type' => 'link',
				'is_active'   => 1,
			)
		);
		$model->create(
			array(
				'source'      => '/off',
				'target_url'  => 'https://example.com/off',
				'match_type'  => 'exact',
				'target_type' => 'link',
				'is_active'   => 0,
			)
		);

		$response = $this->dispatch( 'GET', self::ROUTE, array( 'is_active' => false ) );

		$data = $response->get_data();
		$this->assertCount( 1, $data );
		$this->assertSame( '/off', $data[0]['source'] );
	}

	/**
	 * Logged-in subscribers (no `manage_options`) get rejected with 401/403.
	 */
	public function test_subscriber_is_forbidden(): void {
		$sub = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $sub );

		$response = $this->dispatch( 'GET', self::ROUTE );

		$this->assertContains( $response->get_status(), array( 401, 403 ) );
	}
}
