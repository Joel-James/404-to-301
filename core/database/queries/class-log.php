<?php

namespace DuckDev\WP404\Database\Queries;

// Direct hit? Rest in peace.
defined( 'WPINC' ) || die;

/**
 * Error logs queries class.
 *
 * This class provides the query functionality for the error logs
 * table.
 *
 * @package    WP404
 * @subpackage Query
 * @author     Joel James <me@joelsays.com>
 * @copyright  2020 Joel James
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link       https://duckdev.com/products/404-to-301/
 */

use Exception;
use DuckDev\WP404\Utils\Abstracts\Base;
use Illuminate\Database\Eloquent\Builder;
use DuckDev\WP404\Database\Models\Log as Log_Model;

/**
 * Class Log for queries.
 *
 * @since   4.0.0
 * @package DuckDev\WP404\Database\Queries
 */
class Log extends Base {

	/**
	 * Default arguments to make query.
	 *
	 * @var array
	 *
	 * @since 4.0.0
	 */
	private $default_args = [
		'url'        => '',
		'redirect'   => '',
		'ip'         => '',
		'ref'        => '',
		'ua'         => '',
		'status'     => 1,
		'date'       => '',
		'start_date' => '',
		'end_date'   => '',
		'search'     => '',
	];

	/**
	 * Get a single error log.
	 *
	 * @param int $id Log ID.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function get_log( $id ) {
		try {
			$log = Log_Model::where( 'id', $id )->first();

			if ( empty( $log ) ) {
				$log = [];
			}
		} catch ( Exception $exception ) {
			$log = [];
		}

		return $log;
	}

	/**
	 * Delete a single error log.
	 *
	 * @param int $id Log ID.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function delete_log( $id ) {
		try {
			$success = Log_Model::where( 'id', $id )->delete();

			// Make sure it's boolean.
			if ( empty( $success ) ) {
				$success = false;
			}
		} catch ( Exception $exception ) {
			$success = false;
		}

		return $success;
	}

	/**
	 * Delete multiple error logs.
	 *
	 * @param array $ids Log IDs.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function delete_logs( $ids ) {
		try {
			$success = Log_Model::whereIn( 'id', $ids )->delete();

			// Make sure it's boolean.
			if ( empty( $success ) ) {
				$success = false;
			}
		} catch ( Exception $exception ) {
			$success = false;
		}

		return $success;
	}

	/**
	 * Get error logs.
	 *
	 * @param array $args Query arguments.
	 *
	 * @since 4.0.0
	 */
	public function get_logs( $args ) {
		try {
			$args = $this->get_args( $args );

			$query = Log_Model::status( $args['status'] )
				->url( $args['url'] )
				->referral( $args['ref'] )
				->userAgent( $args['ua'] )
				->ip( $args['ip'] );

			$query = $this->set_search( $args, $query );
			$query = $this->set_sorting( $args, $query );
			$query = $this->set_grouping( $args, $query );
			$query = $this->set_date_query( $args, $query );

			// Take count before paginate.
			if ( empty( $args['group_by'] ) ) {
				$count = $query->count();
			}

			// Set pagination.
			$query = $this->set_pagination( $args, $query );

			$result = $query->get();

			// Take count before paginate.
			if ( ! empty( $args['group_by'] ) ) {
				$count = $result->count();
			}

			$logs = [
				'items' => $result,
				'total' => $count,
			];
		} catch ( Exception $exception ) {
			$logs = [
				'items' => [],
				'total' => 0,
			];
		}

		return $logs;
	}

	/**
	 * Get the default query args array.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	protected function get_args( $args ) {
		$args = wp_parse_args( $args, $this->default_args );

		/**
		 * Filter hook to modify default query arguments for the logs query.
		 *
		 * @param array $args Arguments.
		 *
		 * @since 4.0
		 */
		return apply_filters( '404_to_301_queries_log_args', $args );
	}

	/**
	 * Parse the URL string for query.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	private function set_sorting( $args, $query ) {
		if ( isset( $args['order_by'], $args['order'] ) ) {
			$query->orderBy( $args['order_by'], $args['order'] );
		}

		return $query;
	}

	/**
	 * Parse the URL string for query.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	private function set_grouping( $args, $query ) {
		if ( ! empty( $args['group_by'] ) ) {
			$query->groupBy( $args['group_by'] );
		}

		return $query;
	}

	/**
	 * Parse the URL string for query.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	private function set_pagination( $args, $query ) {
		if ( isset( $args['page'], $args['per_page'] ) ) {
			$page     = (int) $args['page'];
			$per_page = (int) $args['per_page'];

			$offset = ( $page - 1 ) * $per_page;

			$query->offset( $offset )->limit( $per_page );
		}

		return $query;
	}

	/**
	 * Parse the URL string for query.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	private function set_search( $args, $query ) {
		if ( ! empty( $args['search'] ) ) {
			$query->where( 'url', 'like', '%' . $args['search'] . '%' );
		}

		return $query;
	}

	/**
	 * Parse the URL string for query.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	private function set_date_query( $args, $query ) {
		if ( ! empty( $args['date'] ) ) {
			$query->date( $args['date'] );
		} elseif ( ! empty( $args['start_date'] ) && ! empty( $args['end_date'] ) ) {
			$query->dateBetween( $args['start_date'], $args['end_date'] );
		} elseif ( ! empty( $args['start_date'] ) ) {
			$query->dateFrom( $args['start_date'] );
		} elseif ( ! empty( $args['end_date'] ) ) {
			$query->dateTo( $args['end_date'] );
		}

		return $query;
	}
}