<?php
/**
 * Database bootstrap.
 *
 * Instantiates the two BerlinDB-backed tables on every load. BerlinDB
 * hooks each table's `maybe_upgrade()` onto `admin_init`, so the
 * tables come into existence (or upgrade their schema) the first
 * time an admin request runs — without us having to call `dbDelta`
 * manually.
 *
 * During plugin activation we *also* call {@see Database::install_now()}
 * explicitly, because the activation hook fires before `admin_init`
 * and Phase 1 of the v3 -> v4 migration assumes the redirects table
 * already exists.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Database;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use BerlinDB\Database\Table;
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
	 * Logs table instance.
	 *
	 * @since 4.0.0
	 * @var LogsTable|null
	 */
	private $logs;

	/**
	 * Redirects table instance.
	 *
	 * @since 4.0.0
	 * @var RedirectsTable|null
	 */
	private $redirects;

	/**
	 * Bring the tables up.
	 *
	 * Constructing a BerlinDB `Table` is enough on every normal
	 * request — the `Table` registers its own `admin_init` hook
	 * which calls `maybe_upgrade()` and installs the table when
	 * needed. The Activator force-installs synchronously by calling
	 * {@see Database::install_now()} after this runs.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		$this->logs      = new LogsTable();
		$this->redirects = new RedirectsTable();
	}

	/**
	 * Force `maybe_upgrade()` on both tables immediately.
	 *
	 * BerlinDB normally defers install/upgrade to the `admin_init`
	 * hook, which doesn't fire during the activation request. Phase
	 * 1 of the v3 migration runs synchronously from the activation
	 * hook and needs the redirects table on disk, so we call this
	 * explicitly from {@see \DuckDev\FourNotFour\Setup\Activator}.
	 *
	 * Safe to call repeatedly — `maybe_upgrade()` short-circuits
	 * when the schema is already current.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function install_now(): void {
		foreach ( array( $this->logs, $this->redirects ) as $table ) {
			if ( $table instanceof Table ) {
				$table->maybe_upgrade();
			}
		}
	}

	/**
	 * Whether both v4 tables physically exist in the database.
	 *
	 * Used by {@see \DuckDev\FourNotFour\Migration\Migrator::self_heal_phase1()}
	 * to bail out before re-running Phase 1 against tables that
	 * still aren't installed.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function tables_exist(): bool {
		if ( ! $this->logs || ! $this->redirects ) {
			return false;
		}

		return $this->logs->exists() && $this->redirects->exists();
	}

	/**
	 * Get the Logs table instance.
	 *
	 * @since 4.0.0
	 *
	 * @return LogsTable|null
	 */
	public function logs_table(): ?LogsTable {
		return $this->logs;
	}

	/**
	 * Get the Redirects table instance.
	 *
	 * @since 4.0.0
	 *
	 * @return RedirectsTable|null
	 */
	public function redirects_table(): ?RedirectsTable {
		return $this->redirects;
	}
}
