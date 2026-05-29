<?php
/**
 * REST tests for {@see \DuckDev\FourNotFour\Api\Logs}.
 *
 * Exercises the public surface of `/404-to-301/v1/logs*` end-to-end —
 * routes are dispatched through {@see rest_get_server()} so the
 * permission callback, arg validation and response shaping all run.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Models\Logs as LogsModel;

/**
 * Class ApiLogsTest
 *
 * @group api
 */
class ApiLogsTest extends WP_UnitTestCase {

	/**
	 * REST namespace under test.
	 */
	const ROUTE = '/404-to-301/v1/logs';

	/**
	 * Admin user id, used to satisfy the `manage_options` permission
	 * callback. Created per test so the WP_UnitTestCase transaction can
	 * roll it back.
	 *
	 * @var int
	 */
	private $admin_id = 0;

	public function set_up(): void {
		parent::set_up();

		Database::instance();

		$this->admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $this->admin_id );

		// Forces the REST server to boot and `rest_api_init` to fire so
		// every endpoint registers its routes.
		rest_get_server();
	}

	public function tear_down(): void {
		wp_set_current_user( 0 );

		parent::tear_down();
	}

	/**
	 * Helper — dispatch a request through the REST server.
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

	public function test_list_returns_collection_with_pagination_headers(): void {
		$model = LogsModel::instance();
		$model->record_hit( array( 'url' => '/one' ) );
		$model->record_hit( array( 'url' => '/two' ) );
		$model->record_hit( array( 'url' => '/three' ) );

		$response = $this->dispatch( 'GET', self::ROUTE, array( 'per_page' => 2 ) );

		$this->assertSame( 200, $response->get_status() );
		$this->assertCount( 2, $response->get_data() );

		$headers = $response->get_headers();
		$this->assertSame( '3', $headers['X-WP-Total'] );
		$this->assertSame( '2', $headers['X-WP-TotalPages'] );
	}

	public function test_list_filters_by_status(): void {
		$model = LogsModel::instance();
		$ignored = $model->record_hit( array( 'url' => '/ign' ) );
		$model->set_status( $ignored, LogsModel::STATUS_IGNORED );
		$model->record_hit( array( 'url' => '/open' ) );

		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array( 'status' => LogsModel::STATUS_IGNORED )
		);

		$data = $response->get_data();
		$this->assertCount( 1, $data );
		$this->assertSame( '/ign', $data[0]['url'] );
		$this->assertSame( LogsModel::STATUS_IGNORED, $data[0]['status'] );
	}

	public function test_get_returns_shaped_row(): void {
		$id = LogsModel::instance()->record_hit( array( 'url' => '/get-me', 'ua' => 'Mozilla/5.0' ) );

		$response = $this->dispatch( 'GET', self::ROUTE . '/' . $id );

		$this->assertSame( 200, $response->get_status() );
		$body = $response->get_data();
		$this->assertSame( $id, $body['id'] );
		$this->assertSame( '/get-me', $body['url'] );
		$this->assertArrayHasKey( 'status_label', $body );
	}

	public function test_get_returns_404_for_missing_row(): void {
		$response = $this->dispatch( 'GET', self::ROUTE . '/999999' );

		$this->assertSame( 404, $response->get_status() );
		$this->assertSame( 'rest_not_found', $response->get_data()['code'] );
	}

	public function test_update_flips_status(): void {
		$id = LogsModel::instance()->record_hit( array( 'url' => '/patch-me' ) );

		$response = $this->dispatch(
			'PATCH',
			self::ROUTE . '/' . $id,
			array( 'status' => LogsModel::STATUS_FIXED )
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( LogsModel::STATUS_FIXED, $response->get_data()['status'] );
	}

	public function test_update_persists_override_flags(): void {
		$id = LogsModel::instance()->record_hit( array( 'url' => '/overrides' ) );

		$response = $this->dispatch(
			'PATCH',
			self::ROUTE . '/' . $id,
			array(
				'override_redirect' => LogsModel::OVERRIDE_ENABLE,
				'override_email'    => LogsModel::OVERRIDE_DISABLE,
			)
		);

		$body = $response->get_data();
		$this->assertSame( LogsModel::OVERRIDE_ENABLE, $body['override_redirect'] );
		$this->assertSame( LogsModel::OVERRIDE_DISABLE, $body['override_email'] );
		// Untouched columns stay at the default.
		$this->assertSame( LogsModel::OVERRIDE_GLOBAL, $body['override_log'] );
	}

	public function test_update_rejects_out_of_enum_status(): void {
		$id = LogsModel::instance()->record_hit( array( 'url' => '/enum' ) );

		$response = $this->dispatch(
			'PATCH',
			self::ROUTE . '/' . $id,
			array( 'status' => 42 )
		);

		// `enum` constraint kicks in before the callback — REST returns
		// a 400 `rest_invalid_param`.
		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_invalid_param', $response->get_data()['code'] );
	}

	public function test_delete_removes_row(): void {
		$id = LogsModel::instance()->record_hit( array( 'url' => '/delete-me' ) );

		$response = $this->dispatch( 'DELETE', self::ROUTE . '/' . $id );

		$this->assertSame( 200, $response->get_status() );
		$this->assertTrue( $response->get_data()['deleted'] );
		$this->assertNull( LogsModel::instance()->find( $id ) );
	}

	public function test_bulk_delete_removes_every_listed_row(): void {
		$model = LogsModel::instance();
		$ids   = array(
			$model->record_hit( array( 'url' => '/b1' ) ),
			$model->record_hit( array( 'url' => '/b2' ) ),
			$model->record_hit( array( 'url' => '/b3' ) ),
		);

		$response = $this->dispatch( 'DELETE', self::ROUTE, array( 'ids' => $ids ) );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 3, $response->get_data()['deleted'] );
	}

	public function test_bulk_update_flips_status_for_every_id(): void {
		$model = LogsModel::instance();
		$ids   = array(
			$model->record_hit( array( 'url' => '/u1' ) ),
			$model->record_hit( array( 'url' => '/u2' ) ),
		);

		$response = $this->dispatch(
			'POST',
			self::ROUTE . '/bulk-update',
			array(
				'ids'    => $ids,
				'status' => LogsModel::STATUS_IGNORED,
			)
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 2, $response->get_data()['updated'] );

		foreach ( $ids as $id ) {
			$this->assertSame( LogsModel::STATUS_IGNORED, (int) $model->find( $id )->status );
		}
	}

	public function test_requires_authentication(): void {
		wp_set_current_user( 0 );

		$response = $this->dispatch( 'GET', self::ROUTE );

		// `rest_forbidden` (logged-out) → 401, `rest_forbidden` (logged-in
		// without cap) → 403. Either way the request is rejected.
		$this->assertContains( $response->get_status(), array( 401, 403 ) );
	}

	public function test_subscriber_is_forbidden(): void {
		$sub = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $sub );

		$response = $this->dispatch( 'GET', self::ROUTE );

		$this->assertContains( $response->get_status(), array( 401, 403 ) );
	}
}
