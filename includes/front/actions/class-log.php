<?php
/**
 * Log 404 hits to the database.
 *
 * Inserts a new row in `404_to_301_logs` for each fresh 404, or bumps
 * the `hits` counter on the existing row when the URL is already
 * tracked. Honours the plugin's "skip bots" and "skip duplicates"
 * settings.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Front\Actions;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Front\Request;
use DuckDev\FourNotFour\Models\Logs;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class Log
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front\Actions
 */
class Log extends Action {

	/**
	 * Whether this action should fire for the current request.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return bool
	 */
	protected function should_run( Request $request ): bool {
		if ( ! $this->setting( 'logs_enabled', true ) ) {
			return false;
		}

		if ( $request->is_excluded() ) {
			return false;
		}

		// Skip obvious bots when configured to do so.
		if ( $this->setting( 'logs_skip_bots', true ) && ! Helpers::is_human( $request->user_agent() ) ) {
			return false;
		}

		// `logs_skip_duplicates` means: when a row already exists for
		// this URL, do NOT bump the counter — leave it alone entirely.
		if ( $this->setting( 'logs_skip_duplicates', false ) && null !== $request->log() ) {
			return false;
		}

		return true;
	}

	/**
	 * Run the action — insert or bump the log row.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return void
	 */
	public function run( Request $request ): void {
		if ( ! $this->should_run( $request ) ) {
			return;
		}

		$data = array(
			'url'    => $request->url(),
			'ref'    => $request->referer(),
			'ip'     => Helpers::pack_ip( $request->ip() ),
			'ua'     => $request->user_agent(),
			'method' => $request->method(),
		);

		/**
		 * Filter the log row data before it is written to the DB.
		 *
		 * @since 4.0.0
		 *
		 * @param array   $data    Column => value.
		 * @param Request $request Current request.
		 */
		$data = (array) apply_filters( '404_to_301_pre_log_insert', $data, $request );

		$id = Logs::instance()->record_hit( $data );

		// Allow downstream actions to read the log row that was just
		// touched — refresh memoisation so the Request reflects reality.
		$request->refresh_log();

		/**
		 * Fires after a log row has been written.
		 *
		 * @since 4.0.0
		 *
		 * @param int     $id      Log row id (0 on failure).
		 * @param array   $data    Row data that was written.
		 * @param Request $request Current request.
		 */
		do_action( '404_to_301_post_log_insert', $id, $data, $request );
	}
}
