<?php
/**
 * `wp 404-to-301 redirects ...` subcommands.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\CLI;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Models\Redirects as RedirectsModel;
use WP_CLI;

/**
 * Class Redirects
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\CLI
 */
class Redirects extends Command {

	/**
	 * Register the subcommand.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function register(): void {
		WP_CLI::add_command( '404-to-301 redirects', static::class );
	}

	/**
	 * List custom redirects.
	 *
	 * ## OPTIONS
	 *
	 * [--match-type=<type>]
	 * : exact | prefix | regex.
	 *
	 * [--active]
	 * : Only active rows.
	 *
	 * [--per-page=<n>]
	 * : Rows per page (default 20).
	 *
	 * [--page=<n>]
	 * : Page number (default 1).
	 *
	 * [--format=<format>]
	 * : table | csv | json | yaml.
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
			'orderby' => 'id',
			'order'   => 'DESC',
		);

		if ( isset( $assoc['match-type'] ) ) {
			$query['match_type'] = (string) $assoc['match-type'];
		}

		if ( isset( $assoc['active'] ) ) {
			$query['is_active'] = 1;
		}

		$result = RedirectsModel::instance()->paginate( $query );

		$rows = array_map(
			static function ( $row ) {
				return array(
					'id'     => (int) $row->id,
					'source' => (string) $row->source,
					'match'  => (string) $row->match_type,
					'target' => 'page' === $row->target_type
						? '(page #' . (int) $row->target_page_id . ')'
						: (string) $row->target_url,
					'type'   => (int) $row->redirect_type,
					'active' => $row->is_active ? 'yes' : 'no',
					'hits'   => (int) $row->hits,
				);
			},
			$result['items']
		);

		$this->print_rows( $assoc, $rows, array( 'id', 'source', 'match', 'target', 'type', 'active', 'hits' ) );

		WP_CLI::log(
			sprintf(
				/* translators: 1: shown, 2: total */
				__( 'Showing %1$d of %2$d redirects.', '404-to-301' ),
				count( $rows ),
				(int) $result['total']
			)
		);
	}

	/**
	 * Show a single redirect.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Redirect id.
	 *
	 * [--format=<format>]
	 * : table | csv | json | yaml.
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
		$row = RedirectsModel::instance()->find( $id );

		if ( ! $row ) {
			WP_CLI::error( __( 'Redirect not found.', '404-to-301' ) );
		}

		$this->print_rows(
			$assoc,
			array(
				array(
					'id'             => (int) $row->id,
					'source'         => (string) $row->source,
					'match_type'     => (string) $row->match_type,
					'target_type'    => (string) $row->target_type,
					'target_url'     => (string) $row->target_url,
					'target_page_id' => $row->target_page_id,
					'redirect_type'  => (int) $row->redirect_type,
					'is_active'      => (int) $row->is_active,
					'hits'           => (int) $row->hits,
					'created_at'     => $row->created_at,
					'updated_at'     => $row->updated_at,
				),
			),
			array(
				'id',
				'source',
				'match_type',
				'target_type',
				'target_url',
				'target_page_id',
				'redirect_type',
				'is_active',
				'hits',
				'created_at',
				'updated_at',
			)
		);
	}

	/**
	 * Create a new redirect.
	 *
	 * ## OPTIONS
	 *
	 * --source=<source>
	 * : Source URL or pattern.
	 *
	 * --target=<target>
	 * : Destination URL.
	 *
	 * [--type=<status>]
	 * : 301, 302 or 307 (default 301).
	 *
	 * [--match-type=<match>]
	 * : exact | prefix | regex (default exact).
	 *
	 * [--inactive]
	 * : Create the row but leave it inactive.
	 *
	 * [--notes=<notes>]
	 * : Optional admin notes.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function create( array $args, array $assoc ): void {
		$source = (string) ( $assoc['source'] ?? '' );
		$target = (string) ( $assoc['target'] ?? '' );

		if ( '' === $source || '' === $target ) {
			WP_CLI::error( __( '--source and --target are both required.', '404-to-301' ) );
		}

		$id = RedirectsModel::instance()->create(
			array(
				'source'        => $source,
				'target_type'   => 'link',
				'target_url'    => $target,
				'redirect_type' => (int) ( $assoc['type'] ?? 301 ),
				'match_type'    => (string) ( $assoc['match-type'] ?? 'exact' ),
				'is_active'     => isset( $assoc['inactive'] ) ? 0 : 1,
				'notes'         => isset( $assoc['notes'] ) ? (string) $assoc['notes'] : null,
			)
		);

		if ( $id > 0 ) {
			WP_CLI::success(
				sprintf(
					/* translators: %d: id */
					__( 'Redirect created (id %d).', '404-to-301' ),
					$id
				)
			);
		} else {
			WP_CLI::error( __( 'Could not create the redirect.', '404-to-301' ) );
		}
	}

	/**
	 * Update a redirect.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Redirect id.
	 *
	 * [--source=<source>]
	 * : New source.
	 *
	 * [--target=<url>]
	 * : New target URL.
	 *
	 * [--type=<status>]
	 * : New redirect status (301/302/307).
	 *
	 * [--match-type=<match>]
	 * : New match type.
	 *
	 * [--active=<bool>]
	 * : 1 / 0 — flip the active state.
	 *
	 * [--notes=<notes>]
	 * : New notes.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function update( array $args, array $assoc ): void {
		$id   = (int) ( $args[0] ?? 0 );
		$data = array();

		foreach ( array( 'source', 'target', 'type', 'match-type', 'active', 'notes' ) as $key ) {
			if ( ! isset( $assoc[ $key ] ) ) {
				continue;
			}

			switch ( $key ) {
				case 'target':
					$data['target_url'] = (string) $assoc[ $key ];
					break;

				case 'type':
					$data['redirect_type'] = (int) $assoc[ $key ];
					break;

				case 'match-type':
					$data['match_type'] = (string) $assoc[ $key ];
					break;

				case 'active':
					$data['is_active'] = (int) (bool) $assoc[ $key ];
					break;

				case 'notes':
				case 'source':
					$data[ $key ] = (string) $assoc[ $key ];
					break;
			}
		}

		if ( empty( $data ) ) {
			WP_CLI::warning( __( 'Nothing to update — pass at least one --field.', '404-to-301' ) );
			return;
		}

		if ( RedirectsModel::instance()->update( $id, $data ) ) {
			WP_CLI::success(
				sprintf(
					/* translators: %d: id */
					__( 'Redirect #%d updated.', '404-to-301' ),
					$id
				)
			);
		} else {
			WP_CLI::error( __( 'Could not update the redirect.', '404-to-301' ) );
		}
	}

	/**
	 * Delete one or more redirects.
	 *
	 * ## OPTIONS
	 *
	 * [<id>...]
	 * : One or more redirect ids.
	 *
	 * [--all]
	 * : Delete every redirect (asks for confirmation).
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
		$model = RedirectsModel::instance();

		if ( ! empty( $assoc['all'] ) ) {
			WP_CLI::confirm( __( 'Delete every redirect?', '404-to-301' ), $assoc );
			$count = $model->delete_where( array( 'number' => -1 ) );
			WP_CLI::success(
				sprintf(
					/* translators: %d: rows */
					__( 'Deleted %d redirects.', '404-to-301' ),
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
				/* translators: %d: rows */
				__( 'Deleted %d redirects.', '404-to-301' ),
				$deleted
			)
		);
	}
}
