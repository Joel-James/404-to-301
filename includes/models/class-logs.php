<?php
/**
 * Model facade for the 404 logs table.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Models;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Database\Queries\Log as LogQuery;
use DuckDev\FourNotFour\Database\Rows\Log as LogRow;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class Logs
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Models
 */
class Logs extends Model {

	/**
	 * Status constants — mirrors the `status` column on the table.
	 *
	 * @since 4.0.0
	 */
	const STATUS_OPEN    = 0;
	const STATUS_IGNORED = 1;
	const STATUS_FIXED   = 2;

	/**
	 * BerlinDB query class for the logs table.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	protected $query_class = LogQuery::class;

	/**
	 * Find the log row for a normalised URL, if any.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url Raw URL.
	 *
	 * @return LogRow|null
	 */
	public function get_by_url( string $url ) {
		$hash = Helpers::url_hash( $url );

		$query = new LogQuery(
			array(
				'url_hash' => $hash,
				'number'   => 1,
			)
		);

		$items = (array) $query->items;

		return ! empty( $items ) ? $items[0] : null;
	}

	/**
	 * Record a 404 hit: either insert a new row or bump the `hits`
	 * counter on the existing one.
	 *
	 * @since 4.0.0
	 *
	 * @param array $data {
	 *     Column => value. At minimum:
	 *     @type string $url Raw URL.
	 * }
	 *
	 * @return int Row id of the inserted/updated log.
	 */
	public function record_hit( array $data ): int {
		$url = (string) ( $data['url'] ?? '' );

		if ( '' === $url ) {
			return 0;
		}

		$existing = $this->get_by_url( $url );
		$now      = current_time( 'mysql', true );

		if ( $existing instanceof LogRow ) {
			$this->update(
				(int) $existing->id,
				array(
					'hits'       => (int) $existing->hits + 1,
					'updated_at' => $now,
					// Refresh contextual fields so the latest hit is
					// represented in the log.
					'ref'        => (string) ( $data['ref'] ?? $existing->ref ),
					'ip'         => (string) ( $data['ip'] ?? $existing->ip ),
					'ua'         => (string) ( $data['ua'] ?? $existing->ua ),
					'method'     => (string) ( $data['method'] ?? $existing->method ),
				)
			);

			return (int) $existing->id;
		}

		$data['url_hash']   = Helpers::url_hash( $url );
		$data['hits']       = (int) ( $data['hits'] ?? 1 );
		$data['status']     = (int) ( $data['status'] ?? self::STATUS_OPEN );
		$data['created_at'] = $now;
		$data['updated_at'] = $now;

		return $this->create( $data );
	}

	/**
	 * Set the status column on a log row.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id     Row id.
	 * @param int $status One of the STATUS_* constants.
	 *
	 * @return bool
	 */
	public function set_status( int $id, int $status ): bool {
		$allowed = array( self::STATUS_OPEN, self::STATUS_IGNORED, self::STATUS_FIXED );

		if ( ! in_array( $status, $allowed, true ) ) {
			return false;
		}

		return $this->update(
			$id,
			array(
				'status'     => $status,
				'updated_at' => current_time( 'mysql', true ),
			)
		);
	}

	/**
	 * Link a log row to a redirect (typically after the admin creates
	 * one from the Logs page).
	 *
	 * @since 4.0.0
	 *
	 * @param int $id          Log row id.
	 * @param int $redirect_id Redirect row id (0 to clear).
	 *
	 * @return bool
	 */
	public function link_redirect( int $id, int $redirect_id ): bool {
		return $this->update(
			$id,
			array(
				'redirect_id' => $redirect_id > 0 ? $redirect_id : null,
				'status'      => $redirect_id > 0 ? self::STATUS_FIXED : self::STATUS_OPEN,
				'updated_at'  => current_time( 'mysql', true ),
			)
		);
	}

	/**
	 * Delete rows older than the given number of days.
	 *
	 * @since 4.0.0
	 *
	 * @param int $days Cut-off in days.
	 *
	 * @return int Number of rows deleted.
	 */
	public function prune( int $days ): int {
		if ( $days <= 0 ) {
			return 0;
		}

		$cutoff = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return $this->delete_where(
			array(
				'date_query' => array(
					array(
						'column' => 'created_at',
						'before' => $cutoff,
					),
				),
			)
		);
	}
}
