<?php
/**
 * Contract for WP-CLI subcommand classes.
 *
 * Each plugin CLI command implements this interface so the root
 * command (`wp 404-to-301`) can register them uniformly.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Contracts;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Interface Runnable
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Contracts
 */
interface Runnable {

	/**
	 * Register the subcommand with WP-CLI.
	 *
	 * Implementations call `\WP_CLI::add_command()` with the leaf name
	 * (e.g. `'404-to-301 logs'`) and an array of method callbacks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function register(): void;
}
