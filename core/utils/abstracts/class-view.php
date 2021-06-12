<?php
/**
 * Base class for view classes.
 *
 * Extend this class whenever possible to avoid multiple instances
 * of the same classes being created.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @since      4.0.0
 * @subpackage Core
 */

namespace DuckDev\Redirect\Utils\Abstracts;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class View
 *
 * @package DuckDev\Redirect\Utils\Abstracts
 */
abstract class View extends Base {

	/**
	 * Render SVG icon template.
	 *
	 * @param string $name Icon name.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_icon( $name ) {
		// Render icon file.
		$this->render( "components/icons/{$name}" );
	}

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
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render( $file, $args = array(), $once = true ) {
		// Full path to the file.
		$path = DD4T3_DIR . "/app/templates/{$file}.php";

		if ( file_exists( $path ) ) {
			// phpcs:ignore
			extract( $args );

			if ( $once ) {
				/* @noinspection PhpIncludeInspection */
				include_once $path;
			} else {
				/* @noinspection PhpIncludeInspection */
				include $path;
			}
		}
	}
}
