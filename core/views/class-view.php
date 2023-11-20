<?php
/**
 * Base class for view classes.
 *
 * Extend this class whenever possible to avoid multiple instances
 * of the same classes being created.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @subpackage Core
 */

namespace RedirectPress\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use RedirectPress\Utils\Base;

/**
 * Class View
 *
 * @since   4.0.0
 * @extends Base
 * @package RedirectPress\Views
 */
class View extends Base {

	/**
	 * Render a template.
	 *
	 * This will look for the template file inside
	 * /app/templates/{file}.php
	 *
	 * @param string $file File path.
	 * @param array  $args Arguments.
	 * @param bool   $once Should include once.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function render( $file, array $args = array(), $once = false ) {
		// Full path to the file.
		$path = REDIRECTPRESS_DIR . "/app/templates/{$file}.php";

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
	 * @param string $file File path.
	 * @param array  $args Arguments.
	 * @param bool   $once Should include once.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_render( $file, array $args = array(), $once = true ) {
		ob_start();

		// Render the template.
		$this->render( $file, $args, $once );

		return ob_get_clean();
	}

	/**
	 * Get a parameter from the current URL.
	 *
	 * @param string $name    Name of param.
	 * @param string $default Default value.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_param( $name, $default = '' ) {
		// Get param value.
		$value = filter_input( INPUT_GET, $name, FILTER_SANITIZE_STRING );

		// If not exist or fails, use default value.
		if ( is_null( $value ) || false === $value ) {
			$value = $default;
		}

		/**
		 * Filter to modify url param.
		 *
		 * @param mixed  $value   Value.
		 * @param string $name    Name of param.
		 * @param string $default Default value.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'redirectpress_view_get_param', $value );
	}
}
