<?php
/**
 * Deactivation handler.
 *
 * Runs once, whenever the plugin is deactivated. Intentionally minimal:
 * deactivation is not the same as uninstallation, so user data and
 * settings are preserved — full cleanup happens in `uninstall.php`.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Setup;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Deactivator
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Setup
 */
class Deactivator {

	/**
	 * Run deactivation tasks.
	 *
	 * Currently:
	 *  - Pause any in-flight migration jobs so the user can reactivate
	 *    and resume cleanly. No data is removed.
	 *  - Fire `404_to_301_deactivated` so addons can react.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function run(): void {
		if ( class_exists( '\\DuckDev\\FourNotFour\\Migration\\Migrator' ) ) {
			\DuckDev\FourNotFour\Migration\Migrator::instance()->pause();
		}

		/**
		 * Action hook fired right after the plugin is deactivated.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_deactivated' );
	}
}
