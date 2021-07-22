<?php
/**
 * The plugin settings class.
 *
 * This class contains the functionality to manage the settings
 * inside the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Settings
 */

namespace DuckDev\Redirect\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Data\Config;
use DuckDev\Redirect\Utils\Abstracts\Controller;

/**
 * Class Settings
 *
 * @package DuckDev\Redirect\Controllers
 */
class Settings extends Controller {

	/**
	 * Setup plugin class.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		// Register plugin settings with WP.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Get single setting value.
	 *
	 * @param string $key     Setting key.
	 * @param string $module  Module name.
	 * @param array  $default Default values.
	 * @param bool   $valid   Is the setting key and module valid.
	 *
	 * @since  4.0.0
	 *
	 * @return string
	 */
	public static function get( $key, $module = 'general', $default = false, &$valid = true ) {
		// Get module values.
		$values = self::get_module( $module );

		// Value is set.
		if ( isset( $values[ $key ] ) ) {
			$valid = true;
			$value = $values[ $key ];
		} else {
			$valid = false;
			// Use default value if not set.
			$value = $default;
		}

		/**
		 * Filter hook to change the settings capability.
		 *
		 * @param mixed  $value   Setting value.
		 * @param string $key     Setting key.
		 * @param string $module  Module name.
		 * @param array  $default Default values.
		 * @param bool   $valid   Is the setting key and module valid.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_settings_get', $value, $key, $module, $default, $valid );
	}

	/**
	 * Get a module settings values.
	 *
	 * @param string $module  Module name.
	 * @param array  $default Default values.
	 * @param bool   $valid   Is the setting module valid.
	 *
	 * @since  4.0.0
	 *
	 * @return array
	 */
	public static function get_module( $module, $default = array(), &$valid = true ) {
		// Get settings.
		$settings = self::get_settings();

		// Module is set.
		if ( isset( $settings[ $module ] ) ) {
			$valid  = true;
			$values = $settings[ $module ];
		} else {
			$valid = false;
			// Use default values if not set.
			$values = $default;
		}

		/**
		 * Filter hook to modify a module settings before returning it.
		 *
		 * @param array  $values  Values.
		 * @param string $module  Module name.
		 * @param array  $default Default values.
		 * @param bool   $valid   Is the setting module valid.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_settings_get_module', $values, $module, $default, $valid );
	}

	/**
	 * Get the plugin settings data.
	 *
	 * This will return the full settings.
	 * If there are extra fields which is not registered
	 * into default settings, we won't return it.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function get_settings() {
		$values = array();

		// Get settings.
		$settings = get_option( Config::SETTINGS_KEY, array() );
		// Default settings.
		$defaults = self::default_settings();

		// Make sure the data is in proper format.
		foreach ( $defaults as $module => $options ) {
			// If there is nothing set in the current option, we use the default set.
			if ( ! isset( $settings[ $module ] ) ) {
				$settings[ $module ] = $options;
			}

			// Else we combine defaults with current options.
			$values[ $module ] = wp_parse_args( $settings[ $module ], $options );
		}

		/**
		 * Filter hook to change the settings data.
		 *
		 * @param array $values   Settings.
		 * @param array $defaults Default settings.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_settings_get_settings', $values, $defaults );
	}

	/**
	 * Update a single setting value.
	 *
	 * It will only allow registered setting items.
	 *
	 * @param string $key    Setting key.
	 * @param mixed  $value  Setting value.
	 * @param string $module Module name.
	 *
	 * @since  4.0.0
	 *
	 * @return bool
	 */
	public static function update( $key, $value, $module = 'general' ) {
		// Get settings.
		$settings = self::get_settings();

		// Allow only registered items.
		if ( isset( $settings[ $module ][ $key ] ) ) {
			// Set new value.
			$settings[ $module ][ $key ] = $value;

			// Update the values.
			return self::update_module( $settings, $module );
		}

		return false;
	}

	/**
	 * Update a single module settings.
	 *
	 * Handy when updating a module settings only.
	 *
	 * @param array  $values Values.
	 * @param string $module Module name.
	 *
	 * @since  4.0.0
	 *
	 * @return bool
	 */
	public static function update_module( $values, $module = 'general' ) {
		// Get settings.
		$settings = self::get_settings();

		// Allow only registered items.
		if ( isset( $settings[ $module ] ) ) {
			// Set new value.
			$settings[ $module ] = $values;

			// Update the values.
			return self::update_settings( $settings );
		}

		return false;
	}

	/**
	 * Update the entire settings.
	 *
	 * All update functions are using this.
	 *
	 * @param array $values Values.
	 *
	 * @since  4.0.0
	 *
	 * @return bool
	 */
	public static function update_settings( $values ) {
		// Get settings.
		$settings = self::get_settings();

		// Format the settings.
		$settings = self::format_settings( $values, $settings );

		/**
		 * Filter to modify plugin settings before updating it.
		 *
		 * @param array $settings Processed to be updated.
		 * @param array $values   Values passed to update.
		 *
		 * @since 4.0.0
		 */
		$settings = apply_filters( 'dd404_settings_before_update_settings', $settings, $values );

		// Update the options.
		return update_option( Config::SETTINGS_KEY, $settings );
	}

	/**
	 * Get the default settings values.
	 *
	 * To reset plugin back to default, update the
	 * settings with these values.
	 *
	 * @since  4.0.0
	 *
	 * @return array
	 */
	public static function default_settings() {
		$settings = array(
			'general'  => array(
				'guessing' => true,
				'exclude'  => array(),
			),
			'redirect' => array(
				'enable' => true,
				'type'   => '301',
				'target' => 'link',
				'link'   => home_url(),
				'page'   => '',
			),
			'logs'     => array(
				'enable' => true,
			),
			'email'    => array(
				'enable'    => false,
				'recipient' => get_option( 'admin_email' ),
			),
			'misc'     => array(
				'tables' => array(),
			),
		);

		/**
		 * Filter hook to add new item to settings.
		 *
		 * Extensions can hook to this filter for adding new
		 * group of settings.
		 *
		 * @param array $settings Settings.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_settings_default_settings', $settings );
	}

	/**
	 * Format the entire settings before update.
	 *
	 * Should use this to ensure the plugin settings
	 * data is in correct format.
	 *
	 * @param array $new New values.
	 * @param array $old Old values.
	 *
	 * @since  4.0.0
	 *
	 * @return array
	 */
	public static function format_settings( array $new, array $old ) {
		// Only allow registered items.
		foreach ( $old as $module => $options ) {
			// If the module is set.
			if ( isset( $new[ $module ] ) && is_array( $new[ $module ] ) ) {
				foreach ( $options as $key => $value ) {
					// Overwrite with the value from new array.
					if ( isset( $new[ $module ][ $key ] ) ) {
						$old[ $module ][ $key ] = $new[ $module ][ $key ];
					}
				}
			}
		}

		/**
		 * Filter to modify plugin settings formatted result.
		 *
		 * @param array $old Processed to be updated.
		 * @param array $new Values passed to update.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_settings_format_settings', $old, $new );
	}

	/**
	 * Get the list of settings modules.
	 *
	 * @since  4.0.0
	 *
	 * @return array
	 */
	public static function get_modules() {
		// Keys of the settings are modules.
		return array_keys( self::default_settings() );
	}

	/**
	 * Registering 404 to 301 options with WP.
	 *
	 * This function is used to register all settings options to the db using
	 * WordPress settings API.
	 *
	 * @since  2.0.0
	 * @access public
	 * @uses   hooks  register_setting Hook to register our options in db.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			Config::SETTINGS_KEY,
			Config::SETTINGS_KEY,
			array(
				'type'              => 'array',
				'default'           => array(),
				'description'       => __( '404 to 301 plugin settings', '404-to-301' ),
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize plugin settings before save.
	 *
	 * This function is used to register all settings options to the db using
	 * WordPress settings API.
	 *
	 * @param array $values Settings data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function sanitize_settings( array $values ) {
		// Get settings.
		$settings = self::get_settings();

		// Format the settings.
		$settings = self::format_settings( $values, $settings );

		/**
		 * Filter to modify plugin settings before updating it.
		 *
		 * @param array $settings Processed to be updated.
		 * @param array $values   Values passed to update.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_settings_before_update_settings', $settings, $values );
	}
}
