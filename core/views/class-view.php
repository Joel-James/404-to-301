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
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @subpackage Core
 */

namespace DuckDev\Redirect\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Base;

/**
 * Class View
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Views
 */
class View extends Base {

	/**
	 * Render SVG icon template.
	 *
	 * @param string $name   Icon name.
	 * @param int    $width  Width.
	 * @param int    $height Height.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_icon( $name, $width = 6, $height = 6 ) {
		// Render icon file.
		$this->render(
			'components/icons/icon',
			array(
				'icon'   => $name,
				'width'  => $width,
				'height' => $height,
			),
			false
		);
	}

	/**
	 * Render a template.
	 *
	 * This will look for the template file inside
	 * /app/templates/{file}.php
	 *
	 * @param string $file   File path.
	 * @param array  $args   Arguments.
	 * @param bool   $once   Should include once.
	 * @param bool   $return Return content.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render( $file, array $args = array(), $once = true, $return = false ) {
		// Full path to the file.
		$path = DD4T3_DIR . "/app/templates/{$file}.php";

		if ( file_exists( $path ) ) {
			// phpcs:ignore
			extract( $args );

			if ( $once ) {
				/* @noinspection */
				include_once $path;
			} else {
				/* @noinspection */
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
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_render( $file, array $args = array(), $once = true ) {
		ob_start();

		// Render the template.
		$this->render( $file, $args, $once );

		return ob_get_clean();
	}
}
