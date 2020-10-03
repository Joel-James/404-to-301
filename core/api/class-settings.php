<?php
/**
 * The settings API endpoint class.
 *
 * This class handles the API endpoint for managing settings.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @since      4.0.0
 * @subpackage Settings
 */

namespace DuckDev\Redirect\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Utils\Traits\Api;
use DuckDev\Redirect\Utils\Abstracts\Endpoint;

/**
 * Class Settings
 *
 * @package DuckDev\Redirect\Api
 * @since   4.0.0
 */
class Settings extends Endpoint {

	use Api;

	/**
	 * API endpoint for the current api.
	 *
	 * @since 4.0.0
	 *
	 * @var string $endpoint
	 */
	private $endpoint = '/settings';

	/**
	 * Register the routes for handling settings functionality.
	 *
	 * All custom routes for the stats functionality should be registered
	 * here using register_rest_route() function.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function routes() {
		// Routes to manage the settings.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get the plugin settings value.
	 *
	 * This endpoint can be used to get the a single settings
	 * field or the entire values.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_settings( $request ) {
		// Send response.
		return $this->get_response( array() );
	}
}
