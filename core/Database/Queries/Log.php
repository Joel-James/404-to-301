<?php

namespace DuckDev\WP404\Database\Queries;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use IronBound\DB\Manager;
use IronBound\DB\Query\Builder;
use IronBound\DB\Query\Tag\From;
use IronBound\DB\Query\Tag\Where;
use DuckDev\WP404\Database\Models;
use DuckDev\WP404\Database\Tables;
use IronBound\DB\Query\Complex_Query;
use IronBound\DB\Query\Tag\Where_Date;

/**
 * The error log complex query for logs custom table.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Log extends Complex_Query {

	/**
	 * Class constructor.
	 *
	 * @param array $args Query arguments.
	 *
	 * @since 4.0
	 */
	public function __construct( array $args = [] ) {
		parent::__construct(
			Manager::get( Tables\Log::TABLE ),
			$args
		);
	}

	/**
	 * Get the default query args array.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	protected function get_default_args() {
		// Get default args from parent.
		$existing = parent::get_default_args();

		$new = [
			'url'              => '',
			'url__in'          => [],
			'url__not_in'      => [],
			'redirect'         => '',
			'redirect__in'     => [],
			'redirect__not_in' => [],
			'ip'               => '',
			'ip__in'           => [],
			'ip__not_in'       => [],
			'ref'              => '',
			'ref__in'          => [],
			'ref__not_in'      => [],
			'ua'               => '',
			'ua__in'           => [],
			'ua__not_in'       => [],
			'status'           => 'any',
			'start_date'       => '',
			'end_date'         => '',
			'url_search'       => '',
		];

		$args = wp_parse_args( $new, $existing );

		/**
		 * Filter hook to modify default query arguments for the logs query.
		 *
		 * @param array $args Arguments.
		 *
		 * @since 4.0
		 */
		return apply_filters( '404_to_301_database_queries_log_default_args', $args );
	}

	/**
	 * Convert data to log object.
	 *
	 * @param \stdClass $data Data object.
	 *
	 * @since 4.0
	 *
	 * @return Models\Log
	 */
	protected function make_object( \stdClass $data ) {
		return new Models\Log( $data );
	}

	/**
	 * Build the sql query using custom filters for the table.
	 *
	 * This is where we should handle the custom fields filtering
	 * when required.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	protected function build_sql() {
		// Query builder.
		$builder = new Builder();

		// Select query.
		$select = $this->parse_select();

		// Generate from.
		$from = new From( $this->table->get_table_name( $GLOBALS['wpdb'] ), 'q' );

		// Generate WHERE condition.
		$where = new Where( 1, true, 1 );

		// Filters.
		$ip         = $this->parse_ip();
		$url        = $this->parse_url();
		$referral   = $this->parse_ref();
		$user_agent = $this->parse_ua();
		$status     = $this->parse_status();
		$redirect   = $this->parse_redirect();
		$end_date   = $this->parse_end_date();
		$start_date = $this->parse_start_date();
		$url_search = $this->parse_url_search();

		if ( $url ) {
			$where->qAnd( $url );
		}

		if ( $ip ) {
			$where->qAnd( $ip );
		}

		if ( $status ) {
			$where->qAnd( $status );
		}

		if ( $user_agent ) {
			$where->qAnd( $user_agent );
		}

		if ( $redirect ) {
			$where->qAnd( $redirect );
		}

		if ( $referral ) {
			$where->qAnd( $referral );
		}

		if ( $start_date ) {
			$where->qAnd( $start_date );
		}

		if ( $end_date ) {
			$where->qAnd( $end_date );
		}

		if ( $url_search ) {
			$where->qAnd( $url_search );
		}

		// Sorting parser.
		$order = $this->parse_order();

		// Setup pagination.
		$limit = $this->parse_pagination();

		// Setup query.
		$builder->append( $select )
			->append( $from )
			->append( $where )
			->append( $order );

		// Append pagination if required.
		if ( $limit !== null ) {
			$builder->append( $limit );
		}

		$query = $builder->build();

		/**
		 * Filter hook to modify the built query.
		 *
		 * @param array $args Arguments.
		 *
		 * @since 4.0
		 */
		return apply_filters( '404_to_301_database_queries_build_sql', $query );
	}

	/**
	 * Parse the product where.
	 *
	 * @since 1.0
	 *
	 * @return Where|null
	 */
	protected function parse_url() {
		if ( empty( $this->args['url'] ) ) {
			return null;
		} elseif ( ! empty( $this->args['url'] ) ) {
			$this->args['url__in'] = [ $this->args['url'] ];
		}

		return $this->parse_in_or_not_in_query( 'url', $this->args['url__in'], $this->args['url__not_in'] );
	}

	/**
	 * Parse the download where.
	 *
	 * @since 1.0
	 *
	 * @return Where|null
	 */
	protected function parse_ip() {
		if ( empty( $this->args['ref'] ) ) {
			return null;
		} elseif ( ! empty( $this->args['ip'] ) ) {
			$this->args['ip__in'] = [ $this->args['ip'] ];
		}

		return $this->parse_in_or_not_in_query( 'ip', $this->args['ip__in'], $this->args['ip__not_in'] );
	}

	/**
	 * Parse the download where.
	 *
	 * @since 1.0
	 *
	 * @return Where|null
	 */
	protected function parse_ref() {
		if ( empty( $this->args['ref'] ) ) {
			return null;
		} elseif ( ! empty( $this->args['ref'] ) ) {
			$this->args['ref__in'] = [ $this->args['ref'] ];
		}

		return $this->parse_in_or_not_in_query( 'ref', $this->args['ref__in'], $this->args['ref__not_in'] );
	}

	/**
	 * Parse the download where.
	 *
	 * @since 1.0
	 *
	 * @return Where|null
	 */
	protected function parse_ua() {
		if ( empty( $this->args['ua'] ) ) {
			return null;
		} elseif ( ! empty( $this->args['ua'] ) ) {
			$this->args['ua__in'] = [ $this->args['ua'] ];
		}

		return $this->parse_in_or_not_in_query( 'ua', $this->args['ua__in'], $this->args['ua__not_in'] );
	}

	/**
	 * Parse the download where.
	 *
	 * @since 1.0
	 *
	 * @return Where|null
	 */
	protected function parse_redirect() {
		if ( empty( $this->args['redirect'] ) ) {
			return null;
		} elseif ( ! empty( $this->args['redirect'] ) ) {
			$this->args['redirect__in'] = [ $this->args['redirect'] ];
		}

		return $this->parse_in_or_not_in_query( 'ua', $this->args['redirect__in'], $this->args['redirect__not_in'] );
	}

	/**
	 * Parse the version search.
	 *
	 * @since 1.0
	 *
	 * @return Where|null
	 */
	protected function parse_url_search() {
		if ( empty( $this->args['url_search'] ) ) {
			return null;
		}

		return new Where( 'q.url', 'LIKE', esc_sql( $this->args['url_search'] ) );
	}

	/**
	 * Parse the version search.
	 *
	 * @since 1.0
	 *
	 * @return Where|null
	 */
	protected function parse_status() {
		if ( $this->args['status'] === 'any' ) {
			return null;
		}

		return new Where( 'status', true, (int) $this->args['status'] );
	}

	/**
	 * Parse the start date query.
	 *
	 * @since 1.0
	 *
	 * @return Where_Date|null
	 */
	protected function parse_start_date() {
		if ( empty( $this->args['start_date'] ) ) {
			return null;
		} else {
			$date_query = new \WP_Date_Query( $this->args['start_date'], 'q.date' );
			return new Where_Date( $date_query );
		}
	}

	/**
	 * Parse the start date query.
	 *
	 * @since 1.0
	 *
	 * @return Where_Date|null
	 */
	protected function parse_end_date() {
		if ( empty( $this->args['end_date'] ) ) {
			return null;
		} else {
			$date_query = new \WP_Date_Query( $this->args['end_date'], 'q.date' );
			return new Where_Date( $date_query );
		}
	}

}