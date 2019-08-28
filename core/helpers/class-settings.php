<?php

namespace DuckDev\WP404\Helpers;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

/**
 * Define the settings utility functionality.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Settings {

	/**
	 * 404 to 301 settings option name.
	 *
	 * @var string
	 *
	 * @since 4.0
	 */
	private static $key = '404_to_301_settings';

	/**
	 * Get a single setting value.
	 *
	 * @param string $key     Setting key.
	 * @param string $group   Setting group.
	 * @param mixed  $default Default value.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function get_option( $key, $group, $default = false ) {
		// We need key and group.
		if ( empty( $key ) || empty( $group ) ) {
			return false;
		}

		// Get group values.
		$options = self::get_options( $group );

		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Get a setting group values.
	 *
	 * @param string $group Setting group.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return mixed
	 */
	public static function get_options( $group = '' ) {
		// Get site option.
		$options = get_site_option( self::$key, array() );

		/**
		 * Filter to modify settings values before returning.
		 *
		 * @paran array $options Option values.
		 *
		 * @since 4.0
		 */
		$options = apply_filters( '404_to_301_get_options', $options );

		// If group is not given, return all values.
		if ( empty( $group ) ) {
			return $options;
		}

		return isset( $options[ $group ] ) ? $options[ $group ] : array();
	}

	/**
	 * Update a single setting value.
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 * @param string $group Setting group.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return bool False if value was not updated. True if value was updated.
	 */
	public function update_option( $key, $value, $group ) {
		// We need all parameters.
		if ( empty( $key ) || empty( $group ) || empty( $value ) ) {
			return false;
		}

		// Get all values first.
		$options = self::get_options();

		/**
		 * Filter to modify settings values before updating.
		 *
		 * @paran mixed  $value Option value.
		 * @paran string $key Option key.
		 * @paran string $options Option group.
		 *
		 * @since 4.0
		 */
		$value = apply_filters( '404_to_301_update_option', $value, $key, $group );

		$options[ $group ][ $key ] = $value;

		return self::update_options( $options );
	}

	/**
	 * Update a setting group value.
	 *
	 * @param array  $values Setting values.
	 * @param string $group  Setting group.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @return bool False if value was not updated. True if value was updated.
	 */
	public static function update_options( $values, $group = '' ) {
		// We need values.
		if ( empty( $values ) ) {
			return false;
		}

		/**
		 * Filter to modify settings values before updating.
		 *
		 * @paran array $values Option values.
		 *
		 * @since 4.0
		 */
		$values = apply_filters( '404_to_301_update_options', $values );

		// We need values.
		if ( empty( $group ) ) {
			return update_site_option( self::$key, $values );
		} else {
			// Get all settings.
			$settings = (array) self::get_options();

			// Update group values.
			$settings[ $group ] = $values;

			return update_site_option( self::$key, $settings );
		}
	}
}
