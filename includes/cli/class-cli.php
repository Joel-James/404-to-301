<?php
/**
 * Root WP-CLI command.
 *
 * Registers the parent `wp 404-to-301` command and asks each
 * subcommand class to register itself underneath. Doing the parent
 * registration in one place means each subcommand only needs to know
 * its own leaf name.
 *
 * @package FourNotFour
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
				'shortdesc' => __( 'Manage 404 errors and custom redirects from 404 to 301.', '404-to-301' ),
			)
		);

		Logs::register();
		Redirects::register();
		Settings::register();
		Migrate::register();
	}

	/**
	 * Default subcommand — prints a short summary of available verbs.
	 *
	 * ## EXAMPLES
	 *
	 *     wp 404-to-301
	 *     wp 404-to-301 logs list
	 *     wp 404-to-301 redirects create --source=/old --target=https://example.com
	 *     wp 404-to-301 settings get
	 *     wp 404-to-301 migrate status
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function __invoke(): void {
		WP_CLI::log( '404 to 301 — WP-CLI integration' );
		WP_CLI::log( '' );
		WP_CLI::log( 'Subcommands:' );
		WP_CLI::log( '  logs       — list, get, delete, prune log rows' );
		WP_CLI::log( '  redirects  — list, create, update, delete custom redirects' );
		WP_CLI::log( '  settings   — get, update or reset plugin settings' );
		WP_CLI::log( '  migrate    — status, run, abort the v3 → v4 migration' );
		WP_CLI::log( '' );
		WP_CLI::log( 'Run `wp help 404-to-301 <subcommand>` for details on each.' );
	}
}
