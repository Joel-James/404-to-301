<?php
/**
 * Root WP-CLI command.
 *
 * Registers the parent `wp 404-to-301` command and asks each
 * subcommand class to register itself underneath. Doing the parent
 * registration in one place means each subcommand only needs to know
 * its own leaf name.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\CLI;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Contracts\Runnable;
use WP_CLI;

/**
 * Class CLI
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\CLI
 */
class CLI implements Runnable {

	/**
	 * Register the root command and every subcommand.
	 *
	 * Called from {@see \DuckDev\FourNotFour\Core::cli()}.
	 *
	 * The root `404-to-301` command is registered as a namespace (no
	 * `__invoke`) so WP-CLI allows subcommands to hang off it. Calling
	 * `wp 404-to-301` with no subcommand falls back to WP-CLI's built-in
	 * help listing.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( ! class_exists( '\\WP_CLI' ) ) {
			return;
		}

		WP_CLI::add_command(
			'404-to-301',
			static::class,
			array(
				'shortdesc' => 'Manage 404 errors and custom redirects from 404 to 301.',
			)
		);

		Logs::register();
		Redirects::register();
		Settings::register();
		Migrate::register();
		Doctor::register();
	}
}
