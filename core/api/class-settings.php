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
	 * All custom routes for the stats functionality should be registered
	 * here using register_rest_route() function.
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
					'args'                => array(),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(),
				),
			)
		);

		// Routes to manage the entire settings.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '/(?P<module>\w+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_module_settings' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'module' => array(
							'type'        => 'string',
							'required'    => true,
							'enum'        => Options::get_modules(),
							'description' => __( 'Module name.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_module_settings' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'module' => array(
							'type'        => 'string',
							'required'    => true,
							'enum'        => Options::get_modules(),
							'description' => __( 'Module name.', '404-to-301' ),
						),
					),
				),
			)
		);

		// Routes to manage the entire settings.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '/(?P<module>\w+)/(?P<key>\w+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_single_setting' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'key'    => array(
							'type'        => 'string',
							'required'    => true,
							'description' => __( 'Setting key.', '404-to-301' ),
						),
						'module' => array(
							'type'        => 'string',
							'required'    => true,
							'enum'        => Options::get_modules(),
							'description' => __( 'Module name.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_single_setting' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'key'    => array(
							'type'        => 'string',
							'required'    => true,
							'description' => __( 'Setting key.', '404-to-301' ),
						),
						'module' => array(
							'type'        => 'string',
							'required'    => true,
							'enum'        => Options::get_modules(),
							'description' => __( 'Module name.', '404-to-301' ),
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
		// Send response.
		return $this->get_response( Options::get_settings() );
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
	public function get_module_settings( $request ) {
		// Get module name.
		$module = $request->get_param( 'module' );

		// Get value.
		$values = Options::get_module( $module );

		// Send response.
		return $this->get_response(
			array(
				'module' => $module,
				'value'  => $values,
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
	public function get_single_setting( $request ) {
		// Get key.
		$key = $request->get_param( 'key' );
		// Get module name.
		$module = $request->get_param( 'module' );

		// Get value.
		$value = Options::get( $key, $module );

		// Send response.
		return $this->get_response(
			array(
				'key'    => $key,
				'module' => $module,
				'value'  => $value,
			)
		);
	}

	/**
	 * Update the plugin settings values.
	 *
	 * This endpoint will update the whole settings.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function update_settings( $request ) {
		// Get all values.
		$values = $request->get_params();
		// Update the settings.
		$success = Options::update_settings( $values );
		// Get updated settings.
		$settings = Options::get_settings();

		// Send response.
		return $this->get_response( $settings, $success );
	}

	/**
	 * Update the plugin settings values.
	 *
	 * This endpoint will update the whole settings.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function update_module_settings( $request ) {
		// Get all values.
		$values = $request->get_params();
		// Update the settings.
		$success = Options::update_settings( $values );
		// Get updated settings.
		$settings = Options::get_settings();

		// Send response.
		return $this->get_response( $settings, $success );
	}

	/**
	 * Update the plugin settings values.
	 *
	 * This endpoint will update the whole settings.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function update_single_setting( $request ) {
		// Get all values.
		$values = $request->get_params();
		// Update the settings.
		$success = Options::update_settings( $values );
		// Get updated settings.
		$settings = Options::get_settings();

		// Send response.
		return $this->get_response( $settings, $success );
	}
}
