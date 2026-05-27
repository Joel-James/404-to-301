<?php
/**
 * Database bootstrap.
 *
 * Instantiates the two BerlinDB-backed tables on every load — their
 * own `maybe_upgrade()` runs there, so schemas stay in sync with the
 * code without us having to call `dbDelta` manually. Tables write
 * their version into the options table, so the upgrade is no-op once
 * applied.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Tables\Logs as LogsTable;
use DuckDev\FourNotFour\Database\Tables\Redirects as RedirectsTable;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Database
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Database
 */
final class Database extends Singleton {

	/**
	 * Bring the tables up.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		// Constructing a BerlinDB Table runs `maybe_upgrade()` itself,
		// so we don't have to invoke install/upgrade explicitly.
		new LogsTable();
		new RedirectsTable();
	}
}
