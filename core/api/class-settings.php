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

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Utils\Traits\Api;
use DuckDev\Redirect\Utils\Abstracts\Endpoint;
use DuckDev\Redirect\Controllers\Settings as Options;

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
	 * @var string $endpoint
	 *
	 * @since 4.0.0
	 */
	private $endpoint = '/settings';

	/**
	 * Register the routes for handling settings functionality.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function routes() {
		// Routes to manage the entire settings.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'key'    => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Setting key.', '404-to-301' ),
						),
						'module' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => dd404_settings()->get_modules(),
							'description' => __( 'Module name.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'key'    => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Setting key.', '404-to-301' ),
						),
						'module' => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => dd404_settings()->get_modules(),
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
	 * Get the plugin settings value.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
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
			$value = Options::get( $key, $module, false, $valid );

			return $this->get_response(
				array(
					'key'    => $key,
					'module' => $module,
					'value'  => $value,
				),
				$valid
			);
		} elseif ( ! empty( $module ) ) {
			// Get values.
			$values = Options::get_module( $module, false, $valid );

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
		return $this->get_response( Options::get_settings() );
	}

	/**
	 * Update the plugin settings values.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
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
			$success = Options::update( $key, $value, $module );
		} elseif ( ! empty( $module ) ) {
			// Update module settings.
			$success = Options::update_module( $value, $module );
		} else {
			// Update the settings.
			$success = Options::update_settings( $value );
		}

		// Get updated settings.
		$settings = Options::get_settings();

		// Send response.
		return $this->get_response( $settings, $success );
	}
}
