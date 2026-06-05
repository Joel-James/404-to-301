<?php
/**
 * Background-job scheduler abstraction.
 *
 * Detects Action Scheduler at runtime. When available, the migration
 * job is enqueued on AS for retries + persistence + observability;
 * otherwise the WordPress cron event registered by the Migrator does
 * the work in the foreground as a fallback.
 *
 * The class is intentionally tiny — Migrator does the actual chunk
 * processing; Scheduler only decides *who* runs it.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Migration;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Scheduler
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Migration
 */
class Scheduler {

	/**
	 * Action hook AS fires (and that wp-cron also uses) when a
	 * migration chunk needs to run.
	 *
	 * @since 4.0.0
	 */
	const ACTION = '404_to_301_run_migration_chunk';

	/**
	 * Cron schedule key used by wp-cron when AS isn't available.
	 *
	 * @since 4.0.0
	 */
	const CRON_SCHEDULE = 'd404_migration';

	/**
	 * Whether Action Scheduler is loaded and ready.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public static function has_action_scheduler(): bool {
		return function_exists( 'as_enqueue_async_action' )
			|| function_exists( 'as_schedule_single_action' );
	}

	/**
	 * Queue the next migration chunk.
	 *
	 * Prefers AS when available; otherwise falls back to scheduling a
	 * single wp-cron event that fires "now" (cron will run it on the
	 * next request).
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function queue_next_chunk(): void {
		if ( self::has_action_scheduler() && function_exists( 'as_enqueue_async_action' ) ) {
			\as_enqueue_async_action( self::ACTION, array(), '404-to-301' );
			return;
		}

		if ( ! wp_next_scheduled( self::ACTION ) ) {
			wp_schedule_single_event( time() + 1, self::ACTION );
		}
	}

	/**
	 * Cancel every queued chunk — used on deactivate.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function cancel_all(): void {
		if ( self::has_action_scheduler() && function_exists( 'as_unschedule_all_actions' ) ) {
			\as_unschedule_all_actions( self::ACTION, array(), '404-to-301' );
		}

		wp_clear_scheduled_hook( self::ACTION );
	}

	/**
	 * Build the URL that, when visited, installs Action Scheduler
	 * via WordPress's own plugin installer.
	 *
	 * Used by the migration banner when AS isn't loaded but the
	 * current user has `install_plugins`. Includes the nonce
	 * `update.php` expects.
	 *
	 * @since 4.0.0
	 *
	 * @return string Empty string when the current user lacks the cap.
	 */
	public static function install_as_url(): string {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return '';
		}

		if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
			return '';
		}

		return wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=action-scheduler' ),
			'install-plugin_action-scheduler'
		);
	}
}
