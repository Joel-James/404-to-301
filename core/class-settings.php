<?php
/**
 * The plugin settings class.
 *
 * This class contains the functionality to manage the settings
 * inside the plugin.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Settings
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Base;

/**
 * Class Settings
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Controllers
 */
class Settings extends Base {

	/**
	 * Settings key of the plugin.
	 *
	 * @since 4.0.0
	 */
	const KEY = '404_to_301_settings';

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
	 * @since  4.0.0
	 *
	 * @param string $key     Setting key.
	 * @param array  $default Default values.
	 * @param bool   $valid   Is the setting key and module valid.
	 *
	 * @return string
	 */
	public function get( $key, $default = false, &$valid = true ) {
		// Get settings.
		$settings = $this->all();

		// Value is set.
		if ( isset( $settings[ $key ] ) ) {
			$valid = true;
			$value = $settings[ $key ];
		} else {
			$valid = false;
			// Use default value if not set.
			$value = $default;
		}

		/**
		 * Filter hook to change the settings value of single item.
		 *
		 * @since 4.0.0
		 *
		 * @param string $key     Setting key.
		 * @param array  $default Default values.
		 * @param bool   $valid   Is the setting key and module valid.
		 *
		 * @param mixed  $value   Setting value.
		 */
		return apply_filters( 'dd4t3_settings_get', $value, $key, $default, $valid );
	}

	/**
	 * Get the all plugin settings data.
	 *
	 * This will return the full settings.
	 * If there are extra fields which is not registered
	 * into default settings, we won't return it.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $use_default Should use default values as fallback.
	 *
	 * @return array
	 */
	public function all( $use_default = false ) {
		// Get settings.
		$settings = get_option( self::KEY );

		if ( false === $settings ) {
			// Use default settings if asked.
			$settings = $use_default ? $this->defaults() : array();
		}

		/**
		 * Filter hook to change the whole settings data.
		 *
		 * @since 4.0.0
		 *
		 * @param bool  $use_default Should use default values as fallback.
		 *
		 * @param array $settings    Settings.
		 */
		return apply_filters( 'dd4t3_settings_all', $settings, $use_default );
	}

	/**
	 * Update a single setting value.
	 *
	 * It will only allow registered setting items.
	 *
	 * @since  4.0.0
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 *
	 * @return bool
	 */
	public function set( $key, $value ) {
		// Get settings.
		$settings = $this->all();

		// Set value.
		$settings[ $key ] = $value;

		// Update the values.
		return $this->update( $settings );
	}

	/**
	 * Update the entire settings.
	 *
	 * Be careful when you use this. If you don't pass
	 * all items, the missing items will be removed.
	 *
	 * @since 4.0.0
	 *
	 * @param array $values Values.
	 *
	 * @return bool
	 */
	public function update( array $values ) {
		/**
		 * Filter to modify plugin settings before updating it.
		 *
		 * This filter values will be formatted later.
		 *
		 * @since 4.0.0
		 *
		 * @param array $values Values to update.
		 */
		$values = apply_filters( 'dd4t3_settings_pre_update', $values );

		// Get old settings.
		$old_values = $this->all();

		// Now format it.
		$values = $this->format_values( $values );

		// No need to update if values are same, but tell WP it's updated.
		if ( $values === $old_values ) {
			return true;
		}

		// Update the options.
		return update_option( self::KEY, $values );
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
	public function defaults() {
		$settings = array(
			// General.
			'disable_guessing'     => true,
			'monitor_changes'      => false,
			'exclude_paths'        => array(),
			'disable_ip'           => true,
			// Redirects.
			'redirect_enabled'     => true,
			'redirect_type'        => '301',
			'redirect_target'      => 'link',
			'redirect_link'        => home_url(),
			'redirect_page'        => '',
			// Error logs.
			'logs_enabled'         => true,
			'logs_skip_duplicates' => false,
			// Email notification.
			'email_enabled'        => false,
			'email_recipient'      => get_option( 'admin_email' ),
			// Others.
			'plugin_version'       => 0,
			'logs_upgraded'        => true,
		);

		/**
		 * Filter hook to add new item to settings.
		 *
		 * Extensions can hook to this filter for adding new
		 * group of settings.
		 *
		 * @since 4.0.0
		 *
		 * @param array $settings Settings.
		 */
		return apply_filters( 'dd4t3_settings_defaults', $settings );
	}

	/**
	 * Registering 404 to 301 options with WP.
	 *
	 * This function is used to register all settings options to the db using
	 * WordPress settings API.
	 *
	 * @since  4.0.0
	 * @access public
	 * @uses   register_setting
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			self::KEY,
			self::KEY,
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
	 * @since  4.0.0
	 * @access public
	 *
	 * @param array $values Settings data.
	 *
	 * @return array
	 */
	public function sanitize_settings( $values ) {
		// Should be a proper array.
		$values = empty( $values ) || ! is_array( $values ) ? array() : $values;

		/**
		 * Filter to modify plugin settings before updating it.
		 *
		 * This filter values will be formatted later.
		 *
		 * @since 4.0.0
		 *
		 * @param array $values Values to update.
		 */
		$values = apply_filters( 'dd4t3_settings_pre_update', $values );

		// Format the settings.
		return $this->format_values( $values );
	}

	/**
	 * Format the entire settings before update.
	 *
	 * Should use this to ensure the plugin settings
	 * data is in correct format.
	 *
	 * @since  4.0.0
	 *
	 * @param array $values Values to format.
	 *
	 * @return array
	 */
	private function format_values( array $values ) {
		$processed = array();

		// Default items.
		$defaults = $this->defaults();

		// Format the settings.
		foreach ( $defaults as $key => $value ) {
			if ( isset( $values[ $key ] ) ) {
				$processed[ $key ] = $this->sanitize_field( $key, $values[ $key ] );
			} else {
				// If the field is missing, set empty value.
				$processed[ $key ] = $this->get_empty_value( $key );
			}
		}

		/**
		 * Filter to modify plugin settings formatted result.
		 *
		 * @since 4.0.0
		 *
		 * @param array $new Values passed to update.
		 *
		 * @param array $old Processed to be updated.
		 */
		return apply_filters( 'dd4t3_settings_format_values', $processed, $values );
	}

	/**
	 * Get default empty value for the settings.
	 *
	 * This is useful to format the settings if trying to update
	 * settings without all fields.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @param string $key Key.
	 *
	 * @return array|false|string
	 */
	private function get_empty_value( $key ) {
		$value = false;

		// Default values.
		$default = $this->defaults();

		// Check default values to decide value type.
		if ( isset( $default[ $key ] ) ) {
			if ( is_array( $default[ $key ] ) ) {
				$value = array();
			} elseif ( is_string( $default[ $key ] ) ) {
				$value = '';
			}
		}

		return $value;
	}

	/**
	 * Format specially attention required fields.
	 *
	 * Some fields should only accept properly formatted
	 * values. For example an email field.
	 *
	 * @since  4.0.0
	 *
	 * @param string $field Field name.
	 * @param mixed  $value Field value.
	 *
	 * @return array
	 */
	private function sanitize_field( $field, $value ) {
		switch ( $field ) {
			case 'exclude_paths':
				$value = array_filter(
					(array) $value,
					function ( $index, $path ) {
						return '' !== $path;
					},
					ARRAY_FILTER_USE_BOTH
				);
				break;
			case 'redirect_link':
				$value = esc_url_raw( $value );
				break;
			case 'email_recipient':
				$value = is_email( $value ) ? $value : '';
				break;
		}

		return $value;
	}
}
