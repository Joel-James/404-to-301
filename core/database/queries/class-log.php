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
	 * Get error logs.
	 *
	 * @param array $args Query arguments.
	 *
	 * @since 4.0.0
	 */
	public function get_logs( $args ) {
		$args = $this->get_args( $args );

		$query = Log_Model::status( $args['status'] )
			->url( $args['url'] )
			->ref( $args['ref'] )
			->ua( $args['ua'] )
			->ip( $args['ip'] );

		$query = $this->set_date_query( $args, $query );
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