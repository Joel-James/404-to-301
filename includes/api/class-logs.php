<?php
/**
 * 404 logs REST endpoint.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Rows\Log as LogRow;
use DuckDev\FourNotFour\Models\Logs as LogsModel;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Logs
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Api
 */
class Logs extends Endpoint {

	/**
	 * Register routes.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/logs',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'list' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => $this->list_args(),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'bulk_delete' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => array(
						'ids' => array(
							'type'     => 'array',
							'required' => true,
							'items'    => array( 'type' => 'integer' ),
						),
					),
				),
			)
		);

		// Dedicated bulk-update endpoint. Keeps `PATCH /logs/{id}`
		// for single-item updates and gives bulk operations a single
		// round-trip instead of N concurrent requests.
		register_rest_route(
			self::NAMESPACE,
			'/logs/bulk-update',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'bulk_update' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => array(
						'ids'    => array(
							'type'     => 'array',
							'required' => true,
							'items'    => array( 'type' => 'integer' ),
						),
						'status' => array(
							'type' => 'integer',
							'enum' => array( 0, 1, 2 ),
						),
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/logs/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => array(
						'status'            => array(
							'type' => 'integer',
							'enum' => array( 0, 1, 2, 3 ),
						),
						'redirect_id'       => array( 'type' => 'integer' ),
						'override_redirect' => array(
							'type' => 'integer',
							'enum' => array( 0, 1, 2 ),
						),
						'override_log'      => array(
							'type' => 'integer',
							'enum' => array( 0, 1, 2 ),
						),
						'override_email'    => array(
							'type' => 'integer',
							'enum' => array( 0, 1, 2 ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);
	}

	/**
	 * GET /logs.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function list( WP_REST_Request $request ): WP_REST_Response {
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = max( 1, min( 100, (int) $request->get_param( 'per_page' ) ) );

		$args = array(
			'number'  => $per_page,
			'offset'  => ( $page - 1 ) * $per_page,
			'orderby' => (string) ( $request->get_param( 'orderby' ) ? $request->get_param( 'orderby' ) : 'updated_at' ),
			'order'   => strtoupper( (string) ( $request->get_param( 'order' ) ? $request->get_param( 'order' ) : 'DESC' ) ),
		);

		$status = $request->get_param( 'status' );
		if ( null !== $status && '' !== $status ) {
			$args['status'] = (int) $status;
		}

		$search = (string) $request->get_param( 'search' );
		if ( '' !== $search ) {
			$args['search'] = $search;
		}

		$date_from = (string) $request->get_param( 'date_from' );
		$date_to   = (string) $request->get_param( 'date_to' );
		if ( '' !== $date_from || '' !== $date_to ) {
			$range = array( 'column' => 'created_at' );
			if ( '' !== $date_from ) {
				$range['after'] = $date_from;
			}
			if ( '' !== $date_to ) {
				$range['before'] = $date_to;
			}
			$args['date_query'] = array( $range );
		}

		$result = LogsModel::instance()->paginate( $args );

		return $this->collection(
			array_map( array( $this, 'shape' ), $result['items'] ),
			(int) $result['total'],
			$per_page
		);
	}

	/**
	 * GET /logs/{id}.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get( WP_REST_Request $request ) {
		$row = LogsModel::instance()->find( (int) $request['id'] );

		if ( ! $row instanceof LogRow ) {
			return new WP_Error( 'rest_not_found', __( 'Log not found.', '404-to-301' ), array( 'status' => 404 ) );
		}

		return $this->respond( $this->shape( $row ) );
	}

	/**
	 * PATCH /logs/{id} — set the row status, or link to a redirect.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update( WP_REST_Request $request ) {
		$id    = (int) $request['id'];
		$model = LogsModel::instance();
		$row   = $model->find( $id );

		if ( ! $row instanceof LogRow ) {
			return new WP_Error( 'rest_not_found', __( 'Log not found.', '404-to-301' ), array( 'status' => 404 ) );
		}

		$status      = $request->get_param( 'status' );
		$redirect_id = $request->get_param( 'redirect_id' );

		if ( null !== $redirect_id ) {
			$model->link_redirect( $id, (int) $redirect_id );
		}

		if ( null !== $status ) {
			$model->set_status( $id, (int) $status );
		}

		// Per-row override toggles. We accept any subset — anything not
		// in the payload is left untouched on the row.
		$override_keys = array( 'override_redirect', 'override_log', 'override_email' );
		$overrides     = array();

		foreach ( $override_keys as $key ) {
			$value = $request->get_param( $key );
			if ( null !== $value ) {
				$overrides[ $key ] = (int) $value;
			}
		}

		if ( ! empty( $overrides ) ) {
			$fresh = $model->find( $id );
			$model->set_overrides(
				$id,
				array(
					'override_redirect' => $overrides['override_redirect'] ?? (int) $fresh->override_redirect,
					'override_log'      => $overrides['override_log'] ?? (int) $fresh->override_log,
					'override_email'    => $overrides['override_email'] ?? (int) $fresh->override_email,
				)
			);
		}

		return $this->respond( $this->shape( $model->find( $id ) ) );
	}

	/**
	 * DELETE /logs/{id}.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete( WP_REST_Request $request ) {
		$id      = (int) $request['id'];
		$deleted = LogsModel::instance()->delete( $id );

		if ( ! $deleted ) {
			return new WP_Error( 'rest_delete_failed', __( 'Could not delete the log.', '404-to-301' ), array( 'status' => 500 ) );
		}

		return $this->respond(
			array(
				'id'      => $id,
				'deleted' => true,
			)
		);
	}

	/**
	 * DELETE /logs (bulk).
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function bulk_delete( WP_REST_Request $request ): WP_REST_Response {
		$ids   = (array) $request->get_param( 'ids' );
		$model = LogsModel::instance();
		$count = 0;

		foreach ( $ids as $id ) {
			if ( $model->delete( (int) $id ) ) {
				++$count;
			}
		}

		return $this->respond( array( 'deleted' => $count ) );
	}

	/**
	 * POST /logs/bulk-update — flip the status on every selected row
	 * in a single round-trip.
	 *
	 * The React layer used to call `PATCH /logs/{id}` once per id
	 * from `Array.prototype.forEach`, which hammered the API on
	 * larger selections and re-rendered the list after each
	 * response. This endpoint accepts an `ids` array + a single
	 * `status` value and applies them server-side.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response Body shape: `{ updated: int }`.
	 */
	public function bulk_update( WP_REST_Request $request ): WP_REST_Response {
		$ids    = array_map( 'intval', (array) $request->get_param( 'ids' ) );
		$status = $request->get_param( 'status' );
		$model  = LogsModel::instance();
		$count  = 0;

		if ( null === $status ) {
			return $this->respond( array( 'updated' => 0 ) );
		}

		foreach ( $ids as $id ) {
			if ( $id > 0 && $model->set_status( $id, (int) $status ) ) {
				++$count;
			}
		}

		return $this->respond( array( 'updated' => $count ) );
	}

	/**
	 * REST argument schema for the list endpoint.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	private function list_args(): array {
		return array(
			'page'      => array(
				'type'    => 'integer',
				'default' => 1,
			),
			'per_page'  => array(
				'type'    => 'integer',
				'default' => 20,
			),
			'orderby'   => array(
				'type'    => 'string',
				'default' => 'updated_at',
			),
			'order'     => array(
				'type'    => 'string',
				'enum'    => array( 'ASC', 'DESC', 'asc', 'desc' ),
				'default' => 'DESC',
			),
			'search'    => array( 'type' => 'string' ),
			'status'    => array(
				'type' => 'integer',
				'enum' => array( 0, 1, 2 ),
			),
			'date_from' => array( 'type' => 'string' ),
			'date_to'   => array( 'type' => 'string' ),
		);
	}

	/**
	 * Shape a log row for REST.
	 *
	 * @since 4.0.0
	 *
	 * @param LogRow|null $row Row to shape.
	 *
	 * @return array
	 */
	private function shape( $row ): array {
		if ( ! $row instanceof LogRow ) {
			return array();
		}

		$status_label = array(
			LogsModel::STATUS_OPEN    => __( 'Open', '404-to-301' ),
			LogsModel::STATUS_IGNORED => __( 'Ignored', '404-to-301' ),
			LogsModel::STATUS_FIXED   => __( 'Fixed', '404-to-301' ),
			LogsModel::STATUS_CUSTOM  => __( 'Custom redirect', '404-to-301' ),
		);

		return array(
			'id'                => (int) $row->id,
			'url'               => (string) $row->url,
			'ref'               => (string) $row->ref,
			'ip'                => $row->ip(),
			'ua'                => (string) $row->ua,
			'method'            => (string) $row->method,
			'hits'              => (int) $row->hits,
			'status'            => (int) $row->status,
			'status_label'      => $status_label[ (int) $row->status ] ?? '',
			'redirect_id'       => null === $row->redirect_id ? null : (int) $row->redirect_id,
			'override_redirect' => (int) $row->override_redirect,
			'override_log'      => (int) $row->override_log,
			'override_email'    => (int) $row->override_email,
			'created_at'        => $row->created_at,
			'updated_at'        => $row->updated_at,
		);
	}
}
