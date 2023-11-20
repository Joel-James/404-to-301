<?php
/**
 * The data API endpoint class.
 *
 * This class handles the API endpoint for getting data.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Endpoint
 * @subpackage Data
 */

namespace RedirectPress\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use RedirectPress;

/**
 * Class Remote
 *
 * @since   4.0.0
 * @extends Endpoint
 * @package RedirectPress\Api
 */
class Data extends Endpoint {

	/**
	 * API endpoint for the current api.
	 *
	 * @since  4.0.0
	 * @access private
	 * @var string $endpoint
	 */
	private $endpoint = '/data';

	/**
	 * Register the routes for handling data.
	 *
	 * Available endpoints:
	 * - /404-to-301/v1/data/addons - GET
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function routes() {
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '/addons',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_addons' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get the available list of addons.
	 *
	 * Addon list is currently static. We will soon get it from
	 * and external API endpoint.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_addons( $request ) {
		return $this->get_response( RedirectPress\Data::addons() );
	}
}
