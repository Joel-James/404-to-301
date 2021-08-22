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

namespace DuckDev\Redirect\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Utils\Endpoint;

/**
 * Class Settings
 *
 * @since   4.0.0
 * @extends Endpoint
 * @package DuckDev\Redirect\Api
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
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'key'    => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Setting key.', '404-to-301' ),
						),
						'module' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => dd4t3_settings()->get_modules(),
							'description' => __( 'Module name.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'has_access' ),
					'args'                => array(
						'key'    => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Setting key.', '404-to-301' ),
						),
						'module' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => dd4t3_settings()->get_modules(),
							'description' => __( 'Module name.', '404-to-301' ),
						),
						'value'  => array(
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
	 * If module and key is provided, specific setting will be returned.
	 * If only module is given, array of module settings.
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
		$key    = $request->get_param( 'key' );
		$module = $request->get_param( 'module' );

		// Get single setting value.
		if ( ! empty( $key ) && ! empty( $module ) ) {
			// Get value.
			$value = dd4t3_settings()->get( $key, $module, false, $valid );

			return $this->get_response(
				array(
					'key'    => $key,
					'module' => $module,
					'value'  => $value,
				),
				$valid
			);
		} elseif ( ! empty( $module ) ) {
			// Get module values.
			$values = dd4t3_settings()->get_module( $module, false, $valid );

			// Get module settings.
			return $this->get_response(
				array(
					'module' => $module,
					'value'  => $values,
				),
				$valid
			);
		}

		// Get all settings.
		return $this->get_response( dd4t3_settings()->get_settings() );
	}

	/**
	 * Update the plugin settings values.
	 *
	 * If module and key is provided, specific setting will be updated.
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
		// Get parameters.
		$key    = $request->get_param( 'key' );
		$module = $request->get_param( 'module' );
		$value  = $request->get_param( 'value' );

		if ( ! empty( $key ) && ! empty( $module ) && ! empty( $value ) ) {
			// Update single setting value.
			$success = dd4t3_settings()->update( $key, $value, $module );
		} elseif ( ! empty( $module ) ) {
			// Update module settings.
			$success = dd4t3_settings()->update_module( $value, $module );
		} else {
			// Update the settings.
			$success = dd4t3_settings()->update_settings( $value );
		}

		// Get updated settings.
		$settings = dd4t3_settings()->get_settings();

		// Send response.
		return $this->get_response( $settings, $success );
	}
}
