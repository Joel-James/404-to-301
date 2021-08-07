<?php
/**
 * The logs customization API endpoint class.
 *
 * This class handles the API endpoint for logs customization.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @since      4.0.0
 * @subpackage Customization
 */

namespace DuckDev\Redirect\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\Redirect\Data\Redirect;
use DuckDev\Redirect\Utils\Traits\Api;
use DuckDev\Redirect\Utils\Abstracts\Endpoint;

/**
 * Class Settings
 *
 * @package DuckDev\Redirect\Api
 * @since   4.0.0
 */
class Config extends Endpoint {

	use Api;

	/**
	 * API endpoint for the current api.
	 *
	 * @var string $endpoint
	 *
	 * @since 4.0.0
	 */
	private $endpoint = '/logs/config';

	/**
	 * Register the routes for handling logs functionality.
	 *
	 * @since 4.0.0
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
					'callback'            => array( $this, 'create_config' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'log'             => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'The log entry ID.', '404-to-301' ),
						),
						'redirect'        => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array(
								'global',
								'enable',
								'disable',
							),
							'description' => __( 'User agent of the client initiating the request.', '404-to-301' ),
						),
						'email'           => array(
							'type'        => 'string',
							'required'    => false,
							'enum'        => array(
								'global',
								'enable',
								'disable',
							),
							'description' => __( 'IP address of the client initiating the request.', '404-to-301' ),
						),
						'redirect_target' => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'Status of 404 log entry.', '404-to-301' ),
						),
						'redirect_type'   => array(
							'type'        => 'integer',
							'enum'        => array_keys( Redirect::redirect_types() ),
							'description' => __( 'Status of 404 log entry.', '404-to-301' ),
						),
					),
				),
			)
		);

		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_log' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'Log ID to get the details.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_log' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'id'       => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'ID of 404 log entry to update.', '404-to-301' ),
						),
						'url'      => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'The requested URL that caused the 404 log entry.', '404-to-301' ),
						),
						'agent'    => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'User agent of the client initiating the request.', '404-to-301' ),
						),
						'referrer' => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'HTTP referrer of the client initiating the request.', '404-to-301' ),
						),
						'ip'       => array(
							'type'        => 'string',
							'required'    => false,
							'description' => __( 'IP address of the client initiating the request.', '404-to-301' ),
						),
						'status'   => array(
							'type'        => 'integer',
							'required'    => false,
							'enum'        => array( 0, 1 ),
							'description' => __( 'Status of 404 log entry.', '404-to-301' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_log' ),
					'permission_callback' => array( $this, 'check_settings_permission' ),
					'args'                => array(
						'id' => array(
							'type'        => 'integer',
							'required'    => true,
							'description' => __( 'ID of 404 log entry to delete.', '404-to-301' ),
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
			// Get values.
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
