<?php
/**
 * Redirects REST endpoint.
 *
 * Provides list / create / read / update / delete + bulk-delete over
 * the `404_to_301_redirects` table. The React UI on the Redirects
 * page consumes these routes via plain `fetch()` (not core-data), so
 * the response shape is intentionally compact and predictable.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Rows\Redirect as RedirectRow;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;
use DuckDev\FourNotFour\Utils\Helpers;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Redirects
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Api
 */
class Redirects extends Endpoint {

	/**
	 * Register the routes.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/redirects',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'list' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => $this->list_args(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => $this->writable_args( true ),
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

		// Dedicated bulk-update endpoint. Keeps `PATCH /redirects/{id}`
		// for single-item updates and gives bulk operations a single
		// round-trip instead of N concurrent requests.
		register_rest_route(
			self::NAMESPACE,
			'/redirects/bulk-update',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'bulk_update' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => array(
						'ids'           => array(
							'type'     => 'array',
							'required' => true,
							'items'    => array( 'type' => 'integer' ),
						),
						'is_active'     => array( 'type' => 'boolean' ),
						'redirect_type' => array(
							'type' => 'integer',
							// Sourced from the canonical catalogue in
							// `Helpers::redirect_statuses()`. Terminal codes
							// (410/451) are included — they don't redirect; the
							// front controller emits the status header and exits.
							'enum' => Helpers::redirect_status_codes(),
						),
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/redirects/(?P<id>\d+)',
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
					'args'                => $this->writable_args( false ),
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
	 * GET /redirects — paginated list.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function list( WP_REST_Request $request ): WP_REST_Response {
		$args = $this->paging( $request, 'id' );

		// Optional filters.
		foreach ( array( 'match_type', 'target_type', 'redirect_type' ) as $key ) {
			$val = $request->get_param( $key );
			if ( null !== $val && '' !== $val ) {
				$args[ $key ] = $val;
			}
		}

		$active = $request->get_param( 'is_active' );
		if ( null !== $active && '' !== $active ) {
			$args['is_active'] = (int) (bool) $active;
		}

		$search = (string) $request->get_param( 'search' );
		if ( '' !== $search ) {
			$args['search'] = $search;
		}

		$result = RedirectsModel::instance()->paginate( $args );

		return $this->collection(
			array_map( array( $this, 'shape' ), $result['items'] ),
			(int) $result['total'],
			$args['number']
		);
	}

	/**
	 * GET /redirects/{id}.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get( WP_REST_Request $request ) {
		$row = RedirectsModel::instance()->find( (int) $request['id'] );

		if ( ! $row instanceof RedirectRow ) {
			return $this->not_found( __( 'Redirect not found.', '404-to-301' ) );
		}

		return $this->respond( $this->shape( $row ) );
	}

	/**
	 * POST /redirects.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create( WP_REST_Request $request ) {
		$data = $this->collect_writable( $request, true );

		$duplicate = RedirectsModel::instance()->find_by_source(
			(string) ( $data['source'] ?? '' ),
			(string) ( $data['query_handling'] ?? 'ignore' )
		);

		if ( $duplicate instanceof RedirectRow ) {
			return $this->duplicate_error( $duplicate );
		}

		$id = RedirectsModel::instance()->create( $data );

		if ( $id <= 0 ) {
			return $this->error( 'rest_create_failed', __( 'Could not create the redirect.', '404-to-301' ), 500 );
		}

		$row = RedirectsModel::instance()->find( $id );

		return $this->respond( $this->shape( $row ), 201 );
	}

	/**
	 * PUT / PATCH /redirects/{id}.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update( WP_REST_Request $request ) {
		$id  = (int) $request['id'];
		$row = RedirectsModel::instance()->find( $id );

		if ( ! $row instanceof RedirectRow ) {
			return $this->not_found( __( 'Redirect not found.', '404-to-301' ) );
		}

		$data = $this->collect_writable( $request, false );

		// When the user is changing the source / query handling, make
		// sure the new combination doesn't collide with another row.
		if ( isset( $data['source'] ) || isset( $data['query_handling'] ) ) {
			$source         = (string) ( $data['source'] ?? $row->source );
			$query_handling = (string) ( $data['query_handling'] ?? $row->query_handling );
			$duplicate      = RedirectsModel::instance()->find_by_source( $source, $query_handling, $id );

			if ( $duplicate instanceof RedirectRow ) {
				return $this->duplicate_error( $duplicate );
			}
		}

		if ( ! empty( $data ) ) {
			RedirectsModel::instance()->update( $id, $data );
		}

		$row = RedirectsModel::instance()->find( $id );

		return $this->respond( $this->shape( $row ) );
	}

	/**
	 * DELETE /redirects/{id}.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete( WP_REST_Request $request ) {
		$id      = (int) $request['id'];
		$deleted = RedirectsModel::instance()->delete( $id );

		if ( ! $deleted ) {
			return $this->error( 'rest_delete_failed', __( 'Could not delete the redirect.', '404-to-301' ), 500 );
		}

		return $this->respond(
			array(
				'id'      => $id,
				'deleted' => true,
			)
		);
	}

	/**
	 * DELETE /redirects (bulk).
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function bulk_delete( WP_REST_Request $request ): WP_REST_Response {
		$ids     = (array) $request->get_param( 'ids' );
		$model   = RedirectsModel::instance();
		$deleted = 0;

		foreach ( $ids as $id ) {
			if ( $model->delete( (int) $id ) ) {
				++$deleted;
			}
		}

		return $this->respond( array( 'deleted' => $deleted ) );
	}

	/**
	 * POST /redirects/bulk-update — apply a small set of column changes
	 * to every selected row in a single round-trip.
	 *
	 * Supported columns: `is_active`, `redirect_type`. The endpoint
	 * intentionally accepts only a curated subset of writable fields —
	 * bulk-editing `source` or `target_url` makes no sense (every row
	 * would end up identical), and a "set everything" API would be
	 * easy to misuse from the UI.
	 *
	 * Any column not in the payload is left untouched. Passing no
	 * mutating fields is a no-op that returns `{ updated: 0 }`.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response Body shape: `{ updated: int }`.
	 */
	public function bulk_update( WP_REST_Request $request ): WP_REST_Response {
		$ids   = array_map( 'intval', (array) $request->get_param( 'ids' ) );
		$data  = array();
		$model = RedirectsModel::instance();

		$is_active = $request->get_param( 'is_active' );
		if ( null !== $is_active ) {
			$data['is_active'] = (int) (bool) $is_active;
		}

		$redirect_type = $request->get_param( 'redirect_type' );
		if ( null !== $redirect_type ) {
			$data['redirect_type'] = (int) $redirect_type;
		}

		if ( empty( $data ) || empty( $ids ) ) {
			return $this->respond( array( 'updated' => 0 ) );
		}

		$updated = 0;
		foreach ( $ids as $id ) {
			if ( $id > 0 && $model->update( $id, $data ) ) {
				++$updated;
			}
		}

		return $this->respond( array( 'updated' => $updated ) );
	}

	/**
	 * Shape a redirect row for transport over REST.
	 *
	 * @since 4.0.0
	 *
	 * @param RedirectRow|null $row Row to shape.
	 *
	 * @return array
	 */
	/**
	 * Build the 409 response returned when a create / update would
	 * collide with an existing row.
	 *
	 * Carries the conflicting row's id and source in the `data` bag so
	 * the React form can attach the message to the right field (and a
	 * future revision could add an "Edit existing" shortcut).
	 *
	 * @since 4.0.0
	 *
	 * @param RedirectRow $existing The row already using this source.
	 *
	 * @return WP_Error
	 */
	private function duplicate_error( RedirectRow $existing ): WP_Error {
		return new WP_Error(
			'rest_duplicate_source',
			sprintf(
				/* translators: %s: source URL/path of the existing redirect. */
				__( 'A redirect for "%s" already exists. Edit the existing rule instead of creating a duplicate.', '404-to-301' ),
				$existing->source
			),
			array(
				'status'      => 409,
				'field'       => 'source',
				'existing_id' => (int) $existing->id,
				'source'      => (string) $existing->source,
			)
		);
	}

	/**
	 * Shape a RedirectRow into the REST response payload.
	 *
	 * Casts each column to the type the React client expects and resolves
	 * the modifying user's display name so the UI can render it without a
	 * follow-up request.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $row Row instance from the data store. Anything other
	 *                   than a {@see RedirectRow} returns an empty array.
	 *
	 * @return array
	 */
	private function shape( $row ): array {
		if ( ! $row instanceof RedirectRow ) {
			return array();
		}

		$modified_by      = null === $row->modified_by ? null : (int) $row->modified_by;
		$modified_by_name = '';
		if ( $modified_by ) {
			// `display_name` is the canonical human label across the
			// admin UI. `get_userdata()` returns false on a stale id so
			// we degrade gracefully when the user has been deleted.
			$user             = get_userdata( $modified_by );
			$modified_by_name = $user ? (string) $user->display_name : '';
		}

		return array(
			'id'               => (int) $row->id,
			'source'           => (string) $row->source,
			'match_type'       => (string) $row->match_type,
			'target_type'      => (string) $row->target_type,
			'target_url'       => (string) $row->target_url,
			'target_page_id'   => null === $row->target_page_id ? null : (int) $row->target_page_id,
			'redirect_type'    => (int) $row->redirect_type,
			'is_active'        => (bool) $row->is_active,
			'hits'             => (int) $row->hits,
			'last_hit_at'      => $row->last_hit_at,
			'notes'            => $row->notes,
			'query_handling'   => (string) $row->query_handling,
			'modified_by'      => $modified_by,
			'modified_by_name' => $modified_by_name,
			'created_at'       => $row->created_at,
			'updated_at'       => $row->updated_at,
		);
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
			'page'          => array(
				'type'    => 'integer',
				'default' => 1,
			),
			'per_page'      => array(
				'type'    => 'integer',
				'default' => 20,
			),
			'orderby'       => array(
				'type'    => 'string',
				'default' => 'id',
			),
			'order'         => array(
				'type'    => 'string',
				'enum'    => array( 'ASC', 'DESC', 'asc', 'desc' ),
				'default' => 'DESC',
			),
			'search'        => array( 'type' => 'string' ),
			'match_type'    => array(
				'type' => 'string',
				'enum' => array( 'exact', 'prefix', 'regex' ),
			),
			'target_type'   => array(
				'type' => 'string',
				'enum' => array( 'link', 'page', 'none' ),
			),
			'redirect_type' => array( 'type' => 'integer' ),
			'is_active'     => array( 'type' => 'boolean' ),
		);
	}

	/**
	 * REST argument schema for create / update.
	 *
	 * `source` is required on create, optional on update.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $is_create Whether this is a create request.
	 *
	 * @return array
	 */
	private function writable_args( bool $is_create ): array {
		return array(
			'source'         => array(
				'type'     => 'string',
				'required' => $is_create,
			),
			'match_type'     => array(
				'type' => 'string',
				'enum' => array( 'exact', 'prefix', 'regex' ),
			),
			'target_type'    => array(
				'type' => 'string',
				'enum' => array( 'link', 'page', 'none' ),
			),
			'target_url'     => array( 'type' => 'string' ),
			'target_page_id' => array( 'type' => 'integer' ),
			'redirect_type'  => array(
				'type' => 'integer',
				// Sourced from the canonical catalogue in
				// `Helpers::redirect_statuses()`. Terminal codes (410/451)
				// are included — they don't redirect; the front controller
				// emits the status header and exits.
				'enum' => Helpers::redirect_status_codes(),
			),
			'is_active'      => array( 'type' => 'boolean' ),
			'notes'          => array( 'type' => 'string' ),
			'query_handling' => array(
				'type' => 'string',
				'enum' => array( 'ignore', 'preserve', 'require' ),
			),
		);
	}

	/**
	 * Pull the writable columns from a request body.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request   REST request.
	 * @param bool            $with_defaults Apply defaults for missing columns.
	 *
	 * @return array Column => value map (only set keys are included).
	 */
	private function collect_writable( WP_REST_Request $request, bool $with_defaults ): array {
		$keys = array_keys( $this->writable_args( $with_defaults ) );
		$data = array();

		foreach ( $keys as $key ) {
			$value = $request->get_param( $key );

			if ( null === $value ) {
				continue;
			}

			switch ( $key ) {
				case 'source':
				case 'target_url':
				case 'notes':
					$data[ $key ] = sanitize_text_field( (string) $value );
					break;

				case 'match_type':
				case 'target_type':
					$data[ $key ] = (string) $value;
					break;

				case 'query_handling':
					$mode         = (string) $value;
					$data[ $key ] = in_array( $mode, array( 'ignore', 'preserve', 'require' ), true ) ? $mode : 'ignore';
					break;

				case 'target_page_id':
				case 'redirect_type':
					$data[ $key ] = (int) $value;
					break;

				case 'is_active':
					$data[ $key ] = (int) (bool) $value;
					break;
			}
		}

		// Sensible create-time defaults.
		if ( $with_defaults ) {
			$data['match_type']    = $data['match_type'] ?? 'exact';
			$data['target_type']   = $data['target_type'] ?? 'link';
			$data['redirect_type'] = $data['redirect_type'] ?? 301;
			$data['is_active']     = $data['is_active'] ?? 1;
		}

		return $data;
	}
}
