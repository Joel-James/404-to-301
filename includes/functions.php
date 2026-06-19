<?php
/**
 * Global helper functions.
 *
 * Thin convenience aliases that let addons and theme code reach the
 * plugin's long-lived services without remembering the fully qualified
 * class names. Every helper delegates to the service-locator accessors
 * on {@see \DuckDev\FourNotFour\Core}, so swapping the underlying
 * class out at the core level swaps it out everywhere.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'duckdev_404_to_301' ) ) {
	/**
	 * Get the shared plugin Core instance.
	 *
	 * Stable public alias — safe to call from themes, must-use plugins
	 * and addons.
	 *
	 * @since 4.0.0
	 *
	 * @return \DuckDev\FourNotFour\Core
	 */
	function duckdev_404_to_301() {
		return \DuckDev\FourNotFour\Core::instance();
	}
}

if ( ! function_exists( 'duckdev_404_to_301_settings' ) ) {
	/**
	 * Get the shared {@see \DuckDev\FourNotFour\Settings} instance.
	 *
	 * Returns null until P3 wires up the Settings class.
	 *
	 * @since 4.0.0
	 *
	 * @return \DuckDev\FourNotFour\Settings|null
	 */
	function duckdev_404_to_301_settings() {
		return duckdev_404_to_301()->settings();
	}
}
