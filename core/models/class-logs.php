<?php

namespace DuckDev\WP404\Models;

// Direct hit? You must die.
defined( 'WPINC' ) || die;

use WP_Error;
use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * Logs model functionality.
 *
 * @link       https://duckdev.com
 * @since      4.0.0
 * @package    Logs
 * @subpackage Model
 *
 * @author     Joel James <me@joelsays.com>
 */
class Logs extends Base {

	/**
	 * API endpoint for the current endpoint.
	 *
	 * @var string $endpoint
	 *
	 * @since 4.0.0
	 */
	private $table = '/logs/';

	/**
	 * Get a single log item data.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function items( $request ) {
		// Send response.
		return [];
	}

	/**
	 * Update a single log item data.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_log( $request ) {
		// Get the log ID.
		$id = $request->get_param( 'id' );

		// Send response.
		return $this->get_response( [] );
	}

	/**
	 * Delete a single log item.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_log( $request ) {
		// Get the log ID.
		$id = $request->get_param( 'id' );

		// Send response.
		return $this->get_response( [] );
	}
}
