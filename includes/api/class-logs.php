<?php
/**
 * 404 logs REST endpoint.
 *
 * @package DuckDev\FourNotFour
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

		register_rest_route(
			self::NAMESPACE,
			'/logs/summary',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'summary' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/logs/purge',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'purge' ),
					'permission_callback' => array( $this, 'require_access' ),
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
							'enum' => array( 0, 1, 2 ),
						),
						'redirect_id'       => array( 'type' => 'integer' ),
						'override_redirect' => array(
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
		$args = $this->paging( $request, 'updated_at' );

		$search = (string) $request->get_param( 'search' );
		if ( '' !== $search ) {
			$args['search'] = $search;
		}

		$filters = (array) ( $request->get_param( 'filters' ) ?? array() );
		Filter_Mapper::for_logs()->apply( $filters, $args );

		$result = LogsModel::instance()->paginate( $args );

		return $this->collection(
			array_map( array( $this, 'shape' ), $result['items'] ),
			(int) $result['total'],
			$args['number']
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
			return $this->not_found( __( 'Log not found.', '404-to-301' ) );
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
			return $this->not_found( __( 'Log not found.', '404-to-301' ) );
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
		$override_keys = array( 'override_redirect', 'override_email' );
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
			return $this->error( 'rest_delete_failed', __( 'Could not delete the log.', '404-to-301' ), 500 );
		}

		return $this->respond(
			array(
				'id'      => $id,
				'deleted' => true,
			)
		);
	}

	/**
	 * GET /logs/summary — aggregate counts for the dashboard strip.
	 *
	 * @since 4.0.1
	 *
	 * @return WP_REST_Response
	 */
	public function summary(): WP_REST_Response {
		return $this->respond( LogsModel::instance()->summary() );
	}

	/**
	 * DELETE /logs/purge — wipe the entire logs table.
	 *
	 * Custom redirects are stored in a separate table and are untouched.
	 *
	 * @since 4.0.1
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function purge() {
		$ok = LogsModel::instance()->purge_all();

		if ( ! $ok ) {
			return $this->error( 'rest_purge_failed', __( 'Could not purge the logs.', '404-to-301' ), 500 );
		}

		return $this->respond( array( 'purged' => true ) );
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
			'page'     => array(
				'type'    => 'integer',
				'default' => 1,
			),
			'per_page' => array(
				'type'    => 'integer',
				'default' => 20,
			),
			'orderby'  => array(
				'type'    => 'string',
				'default' => 'updated_at',
			),
			'order'    => array(
				'type'    => 'string',
				'enum'    => array( 'ASC', 'DESC', 'asc', 'desc' ),
				'default' => 'DESC',
			),
			'search'   => array( 'type' => 'string' ),
			// DataViews v16 `view.filters` shape: `[ { field, operator,
			// value }, … ]`. Validated and translated into BerlinDB
			// query args by {@see Filter_Mapper::for_logs()}.
			'filters'  => array(
				'type'    => 'array',
				'items'   => array(
					'type'       => 'object',
					'properties' => array(
						'field'    => array(
							'type' => 'string',
							'enum' => array( 'url', 'ref', 'ip', 'hits', 'status' ),
						),
						'operator' => array( 'type' => 'string' ),
						// `value` is mixed (scalar or array depending on
						// operator); validate per-operator inside the mapper.
						'value'    => array(
							'type' => array( 'string', 'integer', 'number', 'boolean', 'array', 'null' ),
						),
					),
					'required'   => array( 'field', 'operator' ),
				),
				'default' => array(),
			),
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
			'override_email'    => (int) $row->override_email,
			'created_at'        => $row->created_at,
			'updated_at'        => $row->updated_at,
		);
	}
}
