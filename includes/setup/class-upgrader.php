<?php
/**
 * Per-load version-bump upgrader.
 *
 * Runs on every request (via `Core::common()`) and checks whether the
 * plugin version in the DB matches the constant baked into the
 * bootstrap. If they differ, runs the upgrade steps for the gap and
 * stamps the new version in the DB.
 *
 * The class is intentionally non-destructive: each step is idempotent,
 * so a partial run followed by a full re-run lands at the same place.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Setup;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Upgrader
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Setup
 */
class Upgrader extends Singleton {

	/**
	 * Option key that stores the plugin version currently installed
	 * in the database.
	 *
	 * @since 4.0.0
	 */
	const VERSION_KEY = '404_to_301_plugin_version';

	/**
	 * Run the version check.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( 'admin_init', array( $this, 'maybe_upgrade' ) );
	}

	/**
	 * Compare the stored version to the runtime version and run any
	 * upgrade steps required to bridge the gap.
	 *
	 * Each step is keyed by the version that introduced it, so adding
	 * a new step is purely additive: declare its method and append a
	 * `version => method` row to the `$steps` array.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function maybe_upgrade(): void {
		$stored  = (string) get_option( self::VERSION_KEY, '0.0.0' );
		$current = D404_VERSION;

		if ( version_compare( $stored, $current, '>=' ) ) {
			return;
		}

		// First boot after an in-place upgrade: the activation hook
		// doesn't fire for the WP one-click updater, automatic
		// background updates, or `wp plugin update`, so the same setup
		// the activator does has to be re-run here. Every step is
		// idempotent — `install_now()` is a no-op when the tables are
		// current, `maybe_migrate_legacy()` returns early once the v4
		// option exists, and `bootstrap_on_activation()` is gated on
		// the `phase1_done` flag — so this is safe to call on every
		// version bump, not just on the v3 → v4 transition.
		$this->run_first_boot_setup();

		// Per-version upgrade callbacks land here as we ship them.
		$steps = array(
			'4.0.1' => array( $this, 'to_4_0_1' ),
		);

		foreach ( $steps as $version => $callback ) {
			if ( version_compare( $stored, $version, '<' ) && is_callable( $callback ) ) {
				$callback();
			}
		}

		update_option( self::VERSION_KEY, $current, false );

		/**
		 * Fires once a version-bump has been applied.
		 *
		 * @since 4.0.0
		 *
		 * @param string $stored  Version the DB was on before the upgrade ran.
		 * @param string $current Version the DB has just been stamped with.
		 */
		do_action( '404_to_301_upgraded', $stored, $current );
	}

	/**
	 * Mirror the activator's setup so the in-place upgrade path (where
	 * the activation hook never fires) still installs tables and
	 * migrates v3 options.
	 *
	 * Deliberately omits the `404_to_301_activated` action — addons
	 * that care about "the plugin just changed version" should listen
	 * for `404_to_301_upgraded` (fired above) instead.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	/**
	 * 4.0.0 → 4.0.1: migrate legacy status = 3 rows.
	 *
	 * In 4.0.0, linking a custom redirect set the log status to 3
	 * ("custom redirect"). That value was removed in 4.0.1 — status
	 * is now a 3-value enum (0 open, 1 ignored, 2 fixed) and
	 * redirect linkage is tracked solely via redirect_id.
	 *
	 * Rows with status = 3 that have an active linked redirect become
	 * Fixed (2). All others (inactive redirect or no redirect) become
	 * Open (0) so they surface for review.
	 *
	 * @since 4.0.1
	 *
	 * @return void
	 */
	private function to_4_0_1(): void {
		global $wpdb;

		$logs_table      = $wpdb->prefix . '404_to_301_logs';
		$redirects_table = $wpdb->prefix . '404_to_301_redirects';

		// Rows with status = 3 whose linked redirect is active → Fixed.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			"UPDATE `{$logs_table}` l
			 INNER JOIN `{$redirects_table}` r ON r.id = l.redirect_id AND r.is_active = 1
			 SET l.status = 2
			 WHERE l.status = 3"
		);

		// Remaining status = 3 rows (inactive or missing redirect) → Open.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			"UPDATE `{$logs_table}` SET status = 0 WHERE status = 3"
		);

		wp_cache_set( 'last_changed', microtime(), '404_to_301_logs' );
	}

	private function run_first_boot_setup(): void {
		if ( class_exists( '\\DuckDev\\FourNotFour\\Database\\Database' ) ) {
			\DuckDev\FourNotFour\Database\Database::instance()->install_now();
		}

		if ( class_exists( '\\DuckDev\\FourNotFour\\Settings' ) ) {
			\DuckDev\FourNotFour\Settings::instance()->maybe_migrate_legacy();
		}

		if ( class_exists( '\\DuckDev\\FourNotFour\\Migration\\Migrator' ) ) {
			\DuckDev\FourNotFour\Migration\Migrator::instance()->bootstrap_on_activation();
		}
	}
}
