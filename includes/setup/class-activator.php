<?php
/**
 * Activation handler.
 *
 * Runs once, whenever the plugin is activated (or reactivated after a
 * deactivate). Keeps the work small and side-effect-free so a normal
 * activation never blocks the user — heavy work (database upgrades,
 * cache warm-up, data migration) is deferred to background processing
 * orchestrated by {@see \DuckDev\FourNotFour\Migration\Migrator}.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Setup;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Activator
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Setup
 */
class Activator {

	/**
	 * Run activation tasks.
	 *
	 * Currently:
	 *  - Ensure the v4 BerlinDB tables exist (created by `Database`'s
	 *    own `maybe_upgrade()` on instantiation).
	 *  - Seed defaults via `Settings::maybe_migrate_legacy()` (which
	 *    maps the v3 `i4t3_gnrl_options` to the v4 schema when present).
	 *  - Detect whether a legacy v3 table exists; if not, flag the
	 *    log migration as already complete so we don't show banners.
	 *  - Fire `404_to_301_activated` so addons can react.
	 *
	 * Each step is wrapped in `class_exists()` so this file can also
	 * sit happily next to a Phase 1 scaffold (no Database / Settings
	 * classes yet) without throwing.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function run(): void {
		// Bring the database layer up. BerlinDB defers `maybe_upgrade()`
		// to the `admin_init` hook, which has NOT fired yet during the
		// activation request. Phase 1 of the migration runs
		// synchronously below and needs both tables on disk — call
		// `install_now()` so we install up-front instead of waiting
		// for `admin_init` to fire on the next admin pageload.
		if ( class_exists( '\\DuckDev\\FourNotFour\\Database\\Database' ) ) {
			\DuckDev\FourNotFour\Database\Database::instance()->install_now();
		}

		// Seed defaults and pick up the v3 option, if any.
		if ( class_exists( '\\DuckDev\\FourNotFour\\Settings' ) ) {
			\DuckDev\FourNotFour\Settings::instance()->maybe_migrate_legacy();
		}

		// Hand legacy-table detection over to the Migrator so it can
		// queue phase-1 (custom-redirect rows) right away.
		if ( class_exists( '\\DuckDev\\FourNotFour\\Migration\\Migrator' ) ) {
			\DuckDev\FourNotFour\Migration\Migrator::instance()->bootstrap_on_activation();
		}

		/**
		 * Action hook fired right after the plugin is activated.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_activated' );
	}
}
