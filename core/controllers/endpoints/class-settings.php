<?php

namespace DuckDev\WP404\Controllers\Endpoints;

// Direct hit? You must die.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\WP404\Utils\Abstracts\Endpoint;
use DuckDev\WP404\Helpers\Settings as Settings_Helper;

/**
 * Settings functionality REST endpoint.
 *
 * @link       https://duckdev.com
 * @since      4.0.0
 * @package    Settings
 * @subpackage Endpoint
 *
 * @author     Joel James <me@joelsays.com>
 */
class Settings extends Endpoint {

	/**
	 * API endpoint for the current endpoint.
	 *
	 * @var string $endpoint
	 *
	 * @since 4.0.0
	 */
	private $endpoint = '/settings/';

	/**
	 * Get current API endpoint url.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_url() {
		return rest_url( $this->get_namespace() . $this->endpoint );
	}

	/**
	 * Register the routes for handling settings functionality.
	 *
	 * Register routes:
	 * - v1/settings - GET to get settings.
	 * - v1/settings - POST, PUT, PATCH to update settings.
	 *
	 * @since 4.0.0
	 */
	public function register_routes() {
		// Route to get all settings.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint, [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_settings' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_settings' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [],
				],
			]
		);

		// Route to get a group settings.
		register_rest_route(
			$this->get_namespace(),
			$this->endpoint . '(?P<group>[a-zA-Z0-9-]+)(?:/(?P<option>[a-zA-Z0-9-]+))?', [
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_settings' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'group'  => [
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param );
							},
						],
						'option' => [
							'validate_callback' => function ( $param ) {
								return is_string( $param );
							},
						],
					],
				],
			]
		);
	}

	/**
	 * Get the settings data.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_settings( $request ) {
		// Get key and group.
		$option = $request->get_param( 'option' );
		$group  = $request->get_param( 'group' );

		if ( $option && $group ) {
			// If a single setting is requested.
			$value = Settings_Helper::get_option( $option, $group );
		} else {
			// If a group or whole settings are requested.
			$value = Settings_Helper::get_options( $group );
		}

		// Send response.
		return $this->get_response( $value );
	}

	/**
	 * Add or update the settings data.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since 4.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_settings( $request ) {
		// We need value.
		if ( ! $request->offsetExists( 'value' ) ) {
			// Send response.
			return $this->get_response( [
				'error' => __( 'Value is not found in the request.', '404-to-301' ),
				false,
			] );
		}

		// Get key and group.
		$option = $request->get_param( 'option' );
		$group  = $request->get_param( 'group' );
		$value  = $request->get_param( 'value' );

		if ( $option && $group ) {
			// If a single setting is requested.
			$updated = Settings_Helper::update_option( $option, $value, $group );
		} else {
			// If a group or whole settings are requested.
			$updated = Settings_Helper::update_options( $value, $group );
		}

		// Send response.
		return $this->get_response( Settings_Helper::get_options( $group ), $updated );
	}

	/**
	 * Check if a given request has access to update a setting.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool
	 */
	public function permissions_check( $request ) {
		/**
		 * Filter to modify settings capability.
		 *
		 * @paran string $cap Capability name.
		 *
		 * @since 4.0.0
		 */
		$settings_cap = apply_filters( '404_to_301_settings_capability', 'manage_options' );

		return current_user_can( $settings_cap );
	}
}
