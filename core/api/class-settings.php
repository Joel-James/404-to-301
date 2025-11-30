<?php
/**
 * The settings API endpoint class.
 *
 * This class handles the API endpoint for managing settings.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @subpackage Settings
 */

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Settings
 *
 * @since   4.0.0
 * @extends Endpoint
 * @package DuckDev\FourNotFour\Api
 */
class Settings extends Endpoint {

	/**
	 * API endpoint for the current api.
	 *
	 * @var string $endpoint
	 * @since  4.0.0
	 * @access private
	 */
	private $endpoint = '/settings';

	/**
	 * Register the routes for handling settings.
	 *
	 * Available endpoints:
	 * - /404-to-301/v1/settings - GET
	 * - /404-to-301/v1/settings - POST
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function routes() {
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'key' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array_keys( duckdev_404_to_301_settings()->defaults() ),
							'description' => __( 'Setting key.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'key'   => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array_keys( duckdev_404_to_301_settings()->defaults() ),
							'description' => __( 'Setting key.', '404-to-301' ),
						),
						'value' => array(
							'required'    => true,
							'description' => __( 'Value(s) to update.', '404-to-301' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Get the plugin setting(s) value.
	 *
	 * If key is provided, specific setting will be returned.
	 * If no params provided, the entire settings will be returned.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function get_settings( $request ) {
		// Get parameters.
		$key = $request->get_param( 'key' );

		// Get single setting value.
		if ( ! empty( $key ) ) {
			// Get value.
			$value = duckdev_404_to_301_settings()->get( $key, false, $valid );

			return $this->get_response(
				array(
					'key'   => $key,
					'value' => $value,
				),
				$valid
			);
		}

		// Get all settings.
		return $this->get_response( duckdev_404_to_301_settings()->all() );
	}

	/**
	 * Update the plugin settings values.
	 *
	 * If key is provided, specific setting will be updated.
	 * If only module is given, module settings will be updated.
	 * If no params provided, the entire settings will be updated.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return WP_REST_Response
	 */
	public function update_settings( $request ) {
		$success = false;
		// Get parameters.
		$key   = $request->get_param( 'key' );
		$value = $request->get_param( 'value' );

		if ( ! empty( $key ) && ! empty( $value ) ) {
			// Update single setting value.
			$success = duckdev_404_to_301_settings()->set( $key, $value );
		} elseif ( ! empty( $value ) ) {
			// Update the settings.
			$success = duckdev_404_to_301_settings()->update( $value );
		}

		// Get updated settings.
		$settings = duckdev_404_to_301_settings()->all();

		// Send response.
		return $this->get_response( $settings, $success );
	}
}
