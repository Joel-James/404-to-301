<?php
/**
 * The core plugin class.
 *
 * This is the main class that initialize the entire plugin functionality.
 * Only one instance of this class be created.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Core
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_CLI;
use WP_CLI\Utils;

/**
 * Class CLI
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Logs extends CLI {

	/**
	 * Get the plugin information.
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The setting name to get or set
	 *
	 * [--set=<value>]
	 * : The value to set
	 *
	 * ## EXAMPLES
	 *
	 *     wp redirection setting name <value>
	 *
	 * @param array $args  Command arguments.
	 * @param array $extra Extra options.
	 *
	 * @return void
	 */
	public function logs( $args, $extra ) {
		$this->success( 'Success!' );
	}
}
