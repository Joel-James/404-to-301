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
 * @package FourNotFour
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

		$steps = array(
			// '4.1.0' => array( $this, 'to_4_1_0' ),
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
}
