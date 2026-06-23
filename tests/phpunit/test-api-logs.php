<?php
/**
 * REST tests for {@see \DuckDev\FourNotFour\Api\Logs}.
 *
 * Exercises the public surface of `/404-to-301/v1/logs*` end-to-end —
 * routes are dispatched through {@see rest_get_server()} so the
 * permission callback, arg validation and response shaping all run.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Models\Logs as LogsModel;
use DuckDev\FourNotFour\Utils\Helpers;

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

	/**
	 * Boot the tables, log in as an admin, and force the REST server
	 * to register every route before each test runs.
	 */
	public function set_up(): void {
		parent::set_up();

		Database::instance();

		$this->admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $this->admin_id );

		// Forces the REST server to boot and `rest_api_init` to fire so
		// every endpoint registers its routes.
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
	 * `GET /logs` returns paginated results with the WP collection headers.
	 */
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

	/**
	 * `GET /logs` with a `status` filter (operator `is`) narrows the
	 * collection to the matching status.
	 */
	public function test_list_filters_by_status_is(): void {
		$model   = LogsModel::instance();
		$ignored = $model->record_hit( array( 'url' => '/ign' ) );
		$model->set_status( $ignored, LogsModel::STATUS_IGNORED );
		$model->record_hit( array( 'url' => '/open' ) );

		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array(
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'is',
						'value'    => LogsModel::STATUS_IGNORED,
					),
				),
			)
		);

		$data = $response->get_data();
		$this->assertCount( 1, $data );
		$this->assertSame( '/ign', $data[0]['url'] );
		$this->assertSame( LogsModel::STATUS_IGNORED, $data[0]['status'] );
	}

	/**
	 * `status` with the `isAny` operator narrows to rows in the
	 * supplied set (delegates to BerlinDB's `__in` arg).
	 */
	public function test_list_filters_by_status_is_any(): void {
		$model   = LogsModel::instance();
		$ignored = $model->record_hit( array( 'url' => '/i' ) );
		$model->set_status( $ignored, LogsModel::STATUS_IGNORED );
		$fixed = $model->record_hit( array( 'url' => '/f' ) );
		$model->set_status( $fixed, LogsModel::STATUS_FIXED );
		$model->record_hit( array( 'url' => '/o' ) );

		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array(
				'filters' => array(
					array(
						'field'    => 'status',
						'operator' => 'isAny',
						'value'    => array( LogsModel::STATUS_IGNORED, LogsModel::STATUS_FIXED ),
					),
				),
			)
		);

		$urls = wp_list_pluck( $response->get_data(), 'url' );
		sort( $urls );
		$this->assertSame( array( '/f', '/i' ), $urls );
	}

	/**
	 * `hits` with the `between` operator emits a numeric `compare_query`
	 * BETWEEN clause.
	 */
	public function test_list_filters_by_hits_between(): void {
		$model = LogsModel::instance();
		$low   = $model->record_hit( array( 'url' => '/low' ) );
		$mid   = $model->record_hit( array( 'url' => '/mid' ) );
		$high  = $model->record_hit( array( 'url' => '/high' ) );

		// Bump the hits counters via repeated record_hit calls so we
		// don't reach into the model internals.
		for ( $i = 0; $i < 4; $i++ ) {
			$model->record_hit( array( 'url' => '/mid' ) );
		}
		for ( $i = 0; $i < 19; $i++ ) {
			$model->record_hit( array( 'url' => '/high' ) );
		}

		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array(
				'filters' => array(
					array(
						'field'    => 'hits',
						'operator' => 'between',
						'value'    => array( 3, 10 ),
					),
				),
			)
		);

		$urls = wp_list_pluck( $response->get_data(), 'url' );
		$this->assertSame( array( '/mid' ), $urls );
	}

	/**
	 * `url` with `contains` runs a LIKE clause on the column.
	 */
	public function test_list_filters_by_url_contains(): void {
		$model = LogsModel::instance();
		$model->record_hit( array( 'url' => '/blog/post-1' ) );
		$model->record_hit( array( 'url' => '/blog/post-2' ) );
		$model->record_hit( array( 'url' => '/landing/page' ) );

		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array(
				'filters' => array(
					array(
						'field'    => 'url',
						'operator' => 'contains',
						'value'    => 'blog',
					),
				),
			)
		);

		$this->assertCount( 2, $response->get_data() );
	}

	/**
	 * `%` and `_` in a `contains` value must be matched literally —
	 * `wpdb::esc_like()` is used to escape them.
	 */
	public function test_list_filters_by_url_contains_escapes_like_wildcards(): void {
		$model = LogsModel::instance();
		$model->record_hit( array( 'url' => '/foo%bar' ) );
		$model->record_hit( array( 'url' => '/fooXbar' ) );

		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array(
				'filters' => array(
					array(
						'field'    => 'url',
						'operator' => 'contains',
						'value'    => '%',
					),
				),
			)
		);

		$urls = wp_list_pluck( $response->get_data(), 'url' );
		$this->assertSame( array( '/foo%bar' ), $urls );
	}

	/**
	 * `ip` filter values are packed via `inet_pton()` before being sent
	 * to the model, so exact-match works against the VARBINARY column.
	 */
	public function test_list_filters_by_ip_is(): void {
		$model = LogsModel::instance();
		$model->record_hit(
			array(
				'url' => '/a',
				'ip'  => Helpers::pack_ip( '192.168.1.1' ),
			)
		);
		$model->record_hit(
			array(
				'url' => '/b',
				'ip'  => Helpers::pack_ip( '10.0.0.1' ),
			)
		);

		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array(
				'filters' => array(
					array(
						'field'    => 'ip',
						'operator' => 'is',
						'value'    => '192.168.1.1',
					),
				),
			)
		);

		$urls = wp_list_pluck( $response->get_data(), 'url' );
		$this->assertSame( array( '/a' ), $urls );
	}

	/**
	 * Filter entries that name a field outside the schema enum are
	 * rejected by REST's schema validator with a 400.
	 */
	public function test_list_rejects_unknown_filter_field(): void {
		$response = $this->dispatch(
			'GET',
			self::ROUTE,
			array(
				'filters' => array(
					array(
						'field'    => 'definitely_not_a_column',
						'operator' => 'is',
						'value'    => 'x',
					),
				),
			)
		);

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_invalid_param', $response->get_data()['code'] );
	}

	/**
	 * `GET /logs/{id}` returns the shaped row for an existing log.
	 */
	public function test_get_returns_shaped_row(): void {
		$id = LogsModel::instance()->record_hit(
			array(
				'url' => '/get-me',
				'ua'  => 'Mozilla/5.0',
			)
		);

		$response = $this->dispatch( 'GET', self::ROUTE . '/' . $id );

		$this->assertSame( 200, $response->get_status() );
		$body = $response->get_data();
		$this->assertSame( $id, $body['id'] );
		$this->assertSame( '/get-me', $body['url'] );
		$this->assertArrayHasKey( 'status_label', $body );
	}

	/**
	 * `GET /logs/{id}` returns 404 when the row doesn't exist.
	 */
	public function test_get_returns_404_for_missing_row(): void {
		$response = $this->dispatch( 'GET', self::ROUTE . '/999999' );

		$this->assertSame( 404, $response->get_status() );
		$this->assertSame( 'rest_not_found', $response->get_data()['code'] );
	}

	/**
	 * `PATCH /logs/{id}` flips the row's status column.
	 */
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

	/**
	 * `PATCH /logs/{id}` writes the override flags it receives and leaves the rest alone.
	 */
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
	}

	/**
	 * REST's `enum` validator rejects out-of-range status values with 400.
	 */
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

	/**
	 * `DELETE /logs/{id}` removes the row.
	 */
	public function test_delete_removes_row(): void {
		$id = LogsModel::instance()->record_hit( array( 'url' => '/delete-me' ) );

		$response = $this->dispatch( 'DELETE', self::ROUTE . '/' . $id );

		$this->assertSame( 200, $response->get_status() );
		$this->assertTrue( $response->get_data()['deleted'] );
		$this->assertNull( LogsModel::instance()->find( $id ) );
	}

	/**
	 * `DELETE /logs` with an `ids` payload removes every listed row.
	 */
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

	/**
	 * `POST /logs/bulk-update` flips the status on every id in one round-trip.
	 */
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

	/**
	 * `GET /logs/summary` returns counts grouped by status plus a
	 * separate `custom` count of rows with a linked redirect.
	 */
	public function test_summary_endpoint_returns_status_and_custom_counts(): void {
		$model     = LogsModel::instance();
		$redirects = \DuckDev\FourNotFour\Models\Redirects::instance();

		// Custom tables aren't rolled back between tests; use a baseline.
		$before     = $model->summary();
		$open_id    = $model->record_hit( array( 'url' => '/api-sum-open' ) );
		$ignored_id = $model->record_hit( array( 'url' => '/api-sum-ignored' ) );
		$linked_log = $model->record_hit( array( 'url' => '/api-sum-linked' ) );

		$model->set_status( $ignored_id, LogsModel::STATUS_IGNORED );

		$redirect_id = $redirects->create(
			array(
				'source'      => '/api-sum-linked',
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);
		$model->link_redirect( $linked_log, $redirect_id );

		$response = $this->dispatch( 'GET', self::ROUTE . '/summary' );
		$this->assertSame( 200, $response->get_status() );

		$body = $response->get_data();
		$this->assertSame( 3, $body['total'] - $before['total'] );
		$this->assertSame( 1, $body['open'] - $before['open'] );
		$this->assertSame( 1, $body['ignored'] - $before['ignored'] );
		$this->assertSame( 1, $body['fixed'] - $before['fixed'] );
		$this->assertSame( 1, $body['custom'] - $before['custom'] );
	}

	/**
	 * `DELETE /logs/purge` truncates the logs table. Custom redirects
	 * live in a separate table and are untouched.
	 */
	public function test_purge_endpoint_truncates_logs(): void {
		$model     = LogsModel::instance();
		$redirects = \DuckDev\FourNotFour\Models\Redirects::instance();

		// `DELETE /logs/purge` issues a TRUNCATE, which is implicit-commit
		// DDL — that breaks WP_UnitTestCase's transaction rollback, so
		// any row this test creates beforehand sticks around in the
		// table on the next run. Use a unique-per-run source to dodge
		// the `source_hash` UNIQUE collision and assert the row count
		// against a baseline.
		$model->record_hit( array( 'url' => '/purge-a' ) );
		$model->record_hit( array( 'url' => '/purge-b' ) );

		$baseline    = (int) $redirects->paginate( array( 'number' => 1 ) )['total'];
		$source      = '/keep-redirect-' . uniqid( '', true );
		$redirect_id = $redirects->create(
			array(
				'source'      => $source,
				'target_url'  => 'https://example.com/',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$response = $this->dispatch( 'DELETE', self::ROUTE . '/purge' );
		$this->assertSame( 200, $response->get_status() );

		$this->assertNull( $model->get_by_url( '/purge-a' ) );
		$this->assertNull( $model->get_by_url( '/purge-b' ) );
		$this->assertNotNull( $redirects->find( $redirect_id ) );
		$this->assertSame( $baseline + 1, (int) $redirects->paginate( array( 'number' => 1 ) )['total'] );

		// Tidy up — TRUNCATE above committed the surrounding tx, so the
		// usual rollback can't undo this row.
		$redirects->delete( $redirect_id );
	}

	/**
	 * Status enum no longer accepts 3 (removed in v4.0.1). Anything
	 * outside [0, 1, 2] is rejected by the REST schema.
	 */
	public function test_update_rejects_legacy_status_3(): void {
		$id = LogsModel::instance()->record_hit( array( 'url' => '/no-status-3' ) );

		$response = $this->dispatch(
			'POST',
			self::ROUTE . '/' . $id,
			array( 'status' => 3 )
		);

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_invalid_param', $response->get_data()['code'] );
	}

	/**
	 * Logged-out requests get rejected with 401/403.
	 */
	public function test_requires_authentication(): void {
		wp_set_current_user( 0 );

		$response = $this->dispatch( 'GET', self::ROUTE );

		// `rest_forbidden` (logged-out) → 401, `rest_forbidden` (logged-in
		// without cap) → 403. Either way the request is rejected.
		$this->assertContains( $response->get_status(), array( 401, 403 ) );
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
