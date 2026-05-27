<?php
/**
 * `wp 404-to-301 logs ...` subcommands.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\CLI;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Models\Logs as LogsModel;
use WP_CLI;

/**
 * Class Logs
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\CLI
 */
class Logs extends Command {

	/**
	 * Register the subcommand.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function register(): void {
		WP_CLI::add_command( '404-to-301 logs', static::class );
	}

	/**
	 * List 404 logs.
	 *
	 * ## OPTIONS
	 *
	 * [--status=<status>]
	 * : Filter by status. 0=open, 1=ignored, 2=fixed.
	 *
	 * [--search=<term>]
	 * : Full-text search across URL / referer / user-agent.
	 *
	 * [--per-page=<n>]
	 * : Rows per page (default 20).
	 *
	 * [--page=<n>]
	 * : Page number (default 1).
	 *
	 * [--format=<format>]
	 * : table | csv | json | yaml (default: table).
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function list( array $args, array $assoc ): void {
		$per_page = isset( $assoc['per-page'] ) ? (int) $assoc['per-page'] : 20;
		$page     = isset( $assoc['page'] ) ? (int) $assoc['page'] : 1;

		$query = array(
			'number'  => max( 1, $per_page ),
			'offset'  => ( max( 1, $page ) - 1 ) * max( 1, $per_page ),
			'orderby' => 'updated_at',
			'order'   => 'DESC',
		);

		if ( isset( $assoc['status'] ) && '' !== $assoc['status'] ) {
			$query['status'] = (int) $assoc['status'];
		}

		if ( ! empty( $assoc['search'] ) ) {
			$query['search'] = (string) $assoc['search'];
		}

		$result = LogsModel::instance()->paginate( $query );

		$rows = array_map(
			static function ( $row ) {
				return array(
					'id'     => (int) $row->id,
					'url'    => (string) $row->url,
					'hits'   => (int) $row->hits,
					'status' => (int) $row->status,
					'first'  => $row->created_at,
					'last'   => $row->updated_at,
				);
			},
			$result['items']
		);

		$this->print_rows( $assoc, $rows, array( 'id', 'url', 'hits', 'status', 'first', 'last' ) );

		WP_CLI::log(
			sprintf(
				/* translators: 1: shown rows, 2: total rows */
				__( 'Showing %1$d of %2$d logs.', '404-to-301' ),
				count( $rows ),
				(int) $result['total']
			)
		);
	}

	/**
	 * Show a single log row.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Log row id.
	 *
	 * [--format=<format>]
	 * : table | csv | json | yaml (default: table).
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function get( array $args, array $assoc ): void {
		$id  = (int) ( $args[0] ?? 0 );
		$row = LogsModel::instance()->find( $id );

		if ( ! $row ) {
			WP_CLI::error( __( 'Log not found.', '404-to-301' ) );
		}

		$this->print_rows(
			$assoc,
			array(
				array(
					'id'         => (int) $row->id,
					'url'        => (string) $row->url,
					'ref'        => (string) $row->ref,
					'ip'         => $row->ip(),
					'ua'         => (string) $row->ua,
					'method'     => (string) $row->method,
					'hits'       => (int) $row->hits,
					'status'     => (int) $row->status,
					'created_at' => $row->created_at,
					'updated_at' => $row->updated_at,
				),
			),
			array( 'id', 'url', 'ref', 'ip', 'ua', 'method', 'hits', 'status', 'created_at', 'updated_at' )
		);
	}

	/**
	 * Set the status of a log row.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Log row id.
	 *
	 * --to=<status>
	 * : Target status: open, ignored or fixed.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function status( array $args, array $assoc ): void {
		$id  = (int) ( $args[0] ?? 0 );
		$map = array(
			'open'    => LogsModel::STATUS_OPEN,
			'ignored' => LogsModel::STATUS_IGNORED,
			'fixed'   => LogsModel::STATUS_FIXED,
		);

		$to = (string) ( $assoc['to'] ?? '' );

		if ( ! isset( $map[ $to ] ) ) {
			WP_CLI::error( __( '`--to` must be one of: open, ignored, fixed.', '404-to-301' ) );
		}

		if ( LogsModel::instance()->set_status( $id, $map[ $to ] ) ) {
			WP_CLI::success(
				sprintf(
					/* translators: 1: id, 2: status */
					__( 'Log #%1$d marked as %2$s.', '404-to-301' ),
					$id,
					$to
				)
			);
		} else {
			WP_CLI::error( __( 'Could not update the log.', '404-to-301' ) );
		}
	}

	/**
	 * Delete one or more log rows.
	 *
	 * ## OPTIONS
	 *
	 * [<id>...]
	 * : One or more log row ids.
	 *
	 * [--status=<status>]
	 * : Delete all rows with this status.
	 *
	 * [--all]
	 * : Delete every row (asks for confirmation).
	 *
	 * [--yes]
	 * : Skip the confirmation prompt for `--all`.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function delete( array $args, array $assoc ): void {
		$model = LogsModel::instance();

		if ( ! empty( $assoc['all'] ) ) {
			WP_CLI::confirm( __( 'Delete every log row?', '404-to-301' ), $assoc );
			$count = $model->delete_where( array( 'number' => -1 ) );
			WP_CLI::success(
				sprintf(
					/* translators: %d: number of rows */
					__( 'Deleted %d logs.', '404-to-301' ),
					$count
				)
			);
			return;
		}

		if ( isset( $assoc['status'] ) ) {
			$count = $model->delete_where(
				array(
					'status' => (int) $assoc['status'],
					'number' => -1,
				)
			);
			WP_CLI::success(
				sprintf(
					/* translators: %d: number of rows */
					__( 'Deleted %d logs.', '404-to-301' ),
					$count
				)
			);
			return;
		}

		$deleted = 0;
		foreach ( $args as $id ) {
			if ( $model->delete( (int) $id ) ) {
				++$deleted;
			}
		}

		WP_CLI::success(
			sprintf(
				/* translators: %d: number of rows */
				__( 'Deleted %d logs.', '404-to-301' ),
				$deleted
			)
		);
	}

	/**
	 * Prune log rows older than N days.
	 *
	 * ## OPTIONS
	 *
	 * [--days=<days>]
	 * : Cut-off in days (default 30).
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function prune( array $args, array $assoc ): void {
		$days  = isset( $assoc['days'] ) ? (int) $assoc['days'] : 30;
		$count = LogsModel::instance()->prune( $days );

		WP_CLI::success(
			sprintf(
				/* translators: 1: rows pruned, 2: days */
				__( 'Pruned %1$d rows older than %2$d days.', '404-to-301' ),
				$count,
				$days
			)
		);
	}
}
