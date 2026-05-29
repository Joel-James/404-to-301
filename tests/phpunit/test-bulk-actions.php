<?php
/**
 * Bulk-action coverage that complements the per-row REST tests.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Models\Logs as LogsModel;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;

/**
 * Class BulkActionsTest
 *
 * @group bulk
 */
class BulkActionsTest extends WP_UnitTestCase {

	const LOGS_ROUTE      = '/404-to-301/v1/logs';
	const REDIRECTS_ROUTE = '/404-to-301/v1/redirects';

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
	 * Bulk delete on logs handles a mix of real and bogus ids — only
	 * the real ones increment the counter.
	 */
	public function test_logs_bulk_delete_ignores_unknown_ids(): void {
		$model = LogsModel::instance();
		$ids   = array(
			$model->record_hit( array( 'url' => '/r1' ) ),
			$model->record_hit( array( 'url' => '/r2' ) ),
		);

		$response = $this->dispatch(
			'DELETE',
			self::LOGS_ROUTE,
			array( 'ids' => array_merge( $ids, array( 999999 ) ) )
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 2, $response->get_data()['deleted'] );

		foreach ( $ids as $id ) {
			$this->assertNull( $model->find( $id ) );
		}
	}

	/**
	 * Bulk update on logs with no `status` payload reports zero updates
	 * (rather than 500ing or flipping rows to a junk value).
	 */
	public function test_logs_bulk_update_without_status_reports_zero(): void {
		$model = LogsModel::instance();
		$ids   = array(
			$model->record_hit( array( 'url' => '/u1' ) ),
			$model->record_hit( array( 'url' => '/u2' ) ),
		);

		$response = $this->dispatch(
			'POST',
			self::LOGS_ROUTE . '/bulk-update',
			array( 'ids' => $ids )
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 0, $response->get_data()['updated'] );

		// Nothing flipped.
		foreach ( $ids as $id ) {
			$this->assertSame( LogsModel::STATUS_OPEN, (int) $model->find( $id )->status );
		}
	}

	/**
	 * Bulk update rejects status values outside its `enum` schema.
	 */
	public function test_logs_bulk_update_rejects_invalid_status_enum(): void {
		$model = LogsModel::instance();
		$ids   = array(
			$model->record_hit( array( 'url' => '/u3' ) ),
		);

		$response = $this->dispatch(
			'POST',
			self::LOGS_ROUTE . '/bulk-update',
			array(
				'ids'    => $ids,
				'status' => 99,
			)
		);

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_invalid_param', $response->get_data()['code'] );
	}

	/**
	 * Bulk update with `ids = []` is a 200/no-op.
	 */
	public function test_logs_bulk_update_with_empty_ids_is_a_no_op(): void {
		$response = $this->dispatch(
			'POST',
			self::LOGS_ROUTE . '/bulk-update',
			array(
				'ids'    => array(),
				'status' => LogsModel::STATUS_IGNORED,
			)
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 0, $response->get_data()['updated'] );
	}

	/**
	 * Bulk delete on redirects removes every listed row.
	 */
	public function test_redirects_bulk_delete_removes_listed_rows(): void {
		$model = RedirectsModel::instance();
		$ids   = array();
		foreach ( array( '/x1', '/x2', '/x3' ) as $src ) {
			$ids[] = $model->create(
				array(
					'source'      => $src,
					'target_url'  => 'https://example.com' . $src,
					'target_type' => 'link',
					'match_type'  => 'exact',
					'is_active'   => 1,
				)
			);
		}

		$response = $this->dispatch(
			'DELETE',
			self::REDIRECTS_ROUTE,
			array( 'ids' => $ids )
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 3, $response->get_data()['deleted'] );

		foreach ( $ids as $id ) {
			$this->assertNull( $model->find( $id ) );
		}
	}

	/**
	 * Bulk delete on redirects without an `ids` payload is rejected
	 * by the REST validator (it's `required` in the schema).
	 */
	public function test_redirects_bulk_delete_requires_ids(): void {
		$response = $this->dispatch( 'DELETE', self::REDIRECTS_ROUTE );

		$this->assertSame( 400, $response->get_status() );
		$this->assertSame( 'rest_missing_callback_param', $response->get_data()['code'] );
	}

	/**
	 * Bulk update flips every targeted row to the requested status.
	 */
	public function test_logs_bulk_update_flips_status_for_each_row(): void {
		$model = LogsModel::instance();
		$ids   = array(
			$model->record_hit( array( 'url' => '/m1' ) ),
			$model->record_hit( array( 'url' => '/m2' ) ),
			$model->record_hit( array( 'url' => '/m3' ) ),
		);

		$response = $this->dispatch(
			'POST',
			self::LOGS_ROUTE . '/bulk-update',
			array(
				'ids'    => $ids,
				'status' => LogsModel::STATUS_IGNORED,
			)
		);

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 3, $response->get_data()['updated'] );

		foreach ( $ids as $id ) {
			$this->assertSame(
				LogsModel::STATUS_IGNORED,
				(int) $model->find( $id )->status
			);
		}
	}
}
