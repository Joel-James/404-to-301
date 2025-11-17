<?php
/**
 * The CLI commands class.
 *
 * This class will register all our custom commands with WP CLI.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    CLI
 * @subpackage CLI
 */

namespace DuckDev\FourNotFour\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_CLI_Command;

/**
 * Class CLI
 *
 * @since   4.0.0
 * @extends WP_CLI_Command
 * @package DuckDev\FourNotFour\CLI
 */
final class CLI extends WP_CLI_Command {

	/**
	 * Get the plugin information.
	 *
	 * ## OPTIONS
	 *
	 * [--item=<value>]
	 * : The info item key (name, slug, version).
	 *
	 * ## EXAMPLES
	 *
	 * wp 404-to-301 info --item=<value>
	 *
	 * @param array $args  Command arguments.
	 * @param array $extra Extra options.
	 *
	 * @return void
	 */
	public function info( $args, $extra ) {
		$info = new Info();
		$info->command( $args, $extra );
	}

	/**
	 * Get the plugin settings.
	 *
	 * ## OPTIONS
	 *
	 * <action>
	 * : The setting action type (get or set).
	 *
	 * [--module=<value>]
	 * : The setting key name.
	 *
	 * [--key=<value>]
	 * : The setting key name.
	 *
	 * [--value=<value>]
	 * : The value to set.
	 *
	 * ## EXAMPLES
	 *
	 * wp 404-to-301 settings <action> --key=<value> --value=<value> --module=<value>
	 *
	 * @param array $args  Command arguments.
	 * @param array $extra Extra options.
	 *
	 * @return void
	 */
	public function settings( $args, $extra ) {
		$settings = new Settings();
		$settings->command( $args, $extra );
	}

	/**
	 * Get the plugin logs.
	 *
	 * ## OPTIONS
	 *
	 * <action>
	 * : The log action to perform (get,clean).
	 *
	 * [--limit=<value>]
	 * : The no. of log items to show. Default limit is 100.
	 *
	 * ## EXAMPLES
	 *
	 * wp 404-to-301 logs <action> --limit=<value>
	 *
	 * @param array $args  Command arguments.
	 * @param array $extra Extra options.
	 *
	 * @return void
	 */
	public function logs( $args, $extra ) {
		$logs = new Logs();
		$logs->command( $args, $extra );
	}
}
