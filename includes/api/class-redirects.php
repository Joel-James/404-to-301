<?php
/**
 * Redirects REST endpoint.
 *
 * Provides list / create / read / update / delete + bulk-delete over
 * the `404_to_301_redirects` table. The React UI on the Redirects
 * page consumes these routes via plain `fetch()` (not core-data), so
 * the response shape is intentionally compact and predictable.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Rows\Redirect as RedirectRow;
use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;
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
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = max( 1, min( 100, (int) $request->get_param( 'per_page' ) ) );

		$args = array(
			'number'  => $per_page,
			'offset'  => ( $page - 1 ) * $per_page,
			'orderby' => (string) ( $request->get_param( 'orderby' ) ?: 'id' ),
			'order'   => strtoupper( (string) ( $request->get_param( 'order' ) ?: 'DESC' ) ),
		);

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
			$per_page
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
			return new WP_Error( 'rest_not_found', __( 'Redirect not found.', '404-to-301' ), array( 'status' => 404 ) );
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

		$id = RedirectsModel::instance()->create( $data );

		if ( $id <= 0 ) {
			return new WP_Error( 'rest_create_failed', __( 'Could not create the redirect.', '404-to-301' ), array( 'status' => 500 ) );
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
			return new WP_Error( 'rest_not_found', __( 'Redirect not found.', '404-to-301' ), array( 'status' => 404 ) );
		}

		$data = $this->collect_writable( $request, false );

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
			return new WP_Error( 'rest_delete_failed', __( 'Could not delete the redirect.', '404-to-301' ), array( 'status' => 500 ) );
		}

		return $this->respond( array( 'id' => $id, 'deleted' => true ) );
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
	 * Shape a redirect row for transport over REST.
	 *
	 * @since 4.0.0
	 *
	 * @param RedirectRow|null $row Row to shape.
	 *
	 * @return array
	 */
	private function shape( $row ): array {
		if ( ! $row instanceof RedirectRow ) {
			return array();
		}

		return array(
			'id'             => (int) $row->id,
			'source'         => (string) $row->source,
			'match_type'     => (string) $row->match_type,
			'target_type'    => (string) $row->target_type,
			'target_url'     => (string) $row->target_url,
			'target_page_id' => null === $row->target_page_id ? null : (int) $row->target_page_id,
			'redirect_type'  => (int) $row->redirect_type,
			'is_active'      => (bool) $row->is_active,
			'hits'           => (int) $row->hits,
			'last_hit_at'    => $row->last_hit_at,
			'notes'          => $row->notes,
			'created_at'     => $row->created_at,
			'updated_at'     => $row->updated_at,
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
			'page'          => array( 'type' => 'integer', 'default' => 1 ),
			'per_page'      => array( 'type' => 'integer', 'default' => 20 ),
			'orderby'       => array( 'type' => 'string', 'default' => 'id' ),
			'order'         => array( 'type' => 'string', 'enum' => array( 'ASC', 'DESC', 'asc', 'desc' ), 'default' => 'DESC' ),
			'search'        => array( 'type' => 'string' ),
			'match_type'    => array( 'type' => 'string', 'enum' => array( 'exact', 'prefix', 'regex' ) ),
			'target_type'   => array( 'type' => 'string', 'enum' => array( 'link', 'page', 'none' ) ),
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
			'source'         => array( 'type' => 'string', 'required' => $is_create ),
			'match_type'     => array( 'type' => 'string', 'enum' => array( 'exact', 'prefix', 'regex' ) ),
			'target_type'    => array( 'type' => 'string', 'enum' => array( 'link', 'page', 'none' ) ),
			'target_url'     => array( 'type' => 'string' ),
			'target_page_id' => array( 'type' => 'integer' ),
			'redirect_type'  => array( 'type' => 'integer', 'enum' => array( 301, 302, 307 ) ),
			'is_active'      => array( 'type' => 'boolean' ),
			'notes'          => array( 'type' => 'string' ),
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
			$data['match_type']    = $data['match_type']    ?? 'exact';
			$data['target_type']   = $data['target_type']   ?? 'link';
			$data['redirect_type'] = $data['redirect_type'] ?? 301;
			$data['is_active']     = $data['is_active']     ?? 1;
		}

		return $data;
	}
}
