<?php
/**
 * The data API endpoint class.
 *
 * This class handles the API endpoint for getting data.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @subpackage Data
 */

namespace DuckDev\Redirect\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect;

/**
 * Class Remote
 *
 * @since   4.0.0
 * @extends Endpoint
 * @package DuckDev\Redirect\Api
 */
class Data extends Endpoint {

	/**
	 * API endpoint for the current api.
	 *
	 * @var string $endpoint
	 * @since  4.0.0
	 * @access private
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
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function get_addons( $request ) {
		// Currently only a few items available.
		return $this->get_response( Redirect\Data::addons() );
	}
}
