<?php
/**
 * Base class for view classes.
 *
 * Extend this class whenever possible to avoid multiple instances
 * of the same classes being created.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    40to301
 * @subpackage Core
 */

namespace DuckDev\FourNotFour\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class View
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Views
 */
class View {

	/**
	 * Render a template.
	 *
	 * This will look for the template file inside
	 * /app/templates/{file}.php
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $file File path.
	 * @param array  $args Arguments.
	 * @param bool   $once Should include once.
	 *
	 * @return void
	 */
	public static function render( string $file, array $args = array(), bool $once = false ): void {
		// Full path to the file.
		$path = DUCKDEV_404_DIR . "/app/templates/{$file}.php";

		if ( file_exists( $path ) ) {
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

			if ( $once ) {
				include_once $path;
			} else {
				include $path;
			}
		}
	}

	/**
	 * Render a template into a variable.
	 *
	 * This will look for the template file inside
	 * /app/templates/{file}.php
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $file File path.
	 * @param array  $args Arguments.
	 * @param bool   $once Should include once.
	 *
	 * @return string
	 */
	public static function get_render( string $file, array $args = array(), bool $once = true ): string {
		ob_start();

		// Render the template.
		self::render( $file, $args, $once );

		return ob_get_clean();
	}

	/**
	 * Get a parameter from the current URL.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @param string $name    Name of param.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public static function get_param( string $name, $default = '' ) {
		// Get param value.
		$value = filter_input( INPUT_GET, $name, FILTER_UNSAFE_RAW );

		// If not exist or fails, use default value.
		if ( is_null( $value ) || false === $value ) {
			$value = $default;
		}

		/**
		 * Filter to modify url param.
		 *
		 * @since 4.0.0
		 *
		 * @param string $name    Name of param.
		 * @param string $default Default value.
		 *
		 * @param mixed  $value   Value.
		 */
		return apply_filters( '404_to_301_view_get_param', $value );
	}
}
