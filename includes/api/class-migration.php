<?php
/**
 * Migration REST endpoint.
 *
 * Three routes:
 *  - `GET    /migration` — status snapshot (used by the banner to poll).
 *  - `POST   /migration` — start phase 2.
 *  - `DELETE /migration` — abort.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Migration\Migrator;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Migration
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Api
 */
class Migration extends Endpoint {

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
			'/migration',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'status' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'start' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'abort' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);

		// Dedicated route for the polling loop — distinct from `start`
		// so abort/POST semantics stay clean.
		register_rest_route(
			self::NAMESPACE,
			'/migration/tick',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'tick' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);
	}

	/**
	 * Return the current migration status.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function status( WP_REST_Request $request ): WP_REST_Response {
		return $this->respond( Migrator::instance()->status() );
	}

	/**
	 * Kick off phase 2.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function start( WP_REST_Request $request ): WP_REST_Response {
		return $this->respond( Migrator::instance()->start_phase2() );
	}

	/**
	 * Process a single migration chunk on demand.
	 *
	 * Drives the React polling loop — each POST processes a chunk and
	 * returns updated status.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function tick( WP_REST_Request $request ): WP_REST_Response {
		return $this->respond( Migrator::instance()->tick() );
	}

	/**
	 * Abort an in-flight migration.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function abort( WP_REST_Request $request ): WP_REST_Response {
		return $this->respond( Migrator::instance()->abort() );
	}
}
