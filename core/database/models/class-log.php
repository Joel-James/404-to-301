<?php

namespace DuckDev\WP404\Database\Models;

// Direct hit? Rest in peace.
defined( 'WPINC' ) || die;

/**
 * Error logs model class.
 *
 * This class extends the model class of ORM to provide the
 * query builder functionality for the custom table.
 *
 * @package    WP404
 * @subpackage Model
 * @author     Joel James <me@joelsays.com>
 * @copyright  2020 Joel James
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link       https://duckdev.com/products/404-to-301/
 */

use DuckDev\WP404\Utils\Abstracts\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Log.
 *
 * @since   4.0.0
 * @package DuckDev\WP404\Database\Models
 */
class Log extends Model {

	/**
	 * Name of the custom table without prefix.
	 *
	 * @var string
	 *
	 * @since 4.0.0
	 */
	protected $table = '404_to_301';


	/**
	 * Columns that can be edited.
	 *
	 * @var array
	 *
	 * @since 4.0.0
	 */
	protected $fillable = [
		'url',
		'ref',
		'ip',
		'ua',
		'options',
		'redirect',
		'status',
	];

	/**
	 * Filter log by log status.
	 *
	 * @param Builder $query  Query builder instance.
	 * @param int     $status Log status (1 or 0).
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeStatus( $query, $status = 1 ) {
		return $query->where( 'status', '=', (int) $status );
	}

	/**
	 * Filter log by error url.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $url   Error url string.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeUrl( $query, $url ) {
		if ( ! empty( $url ) ) {
			return $query->where( 'url', '=', esc_sql( $url ) );
		}

		return $query;
	}

	/**
	 * Filter log by error url parts.
	 *
	 * We use LIKE query to get this.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $url   Error url string.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeUrlLike( $query, $url ) {
		if ( ! empty( $url ) ) {
			return $query->where( 'url', 'like', '%' . esc_sql( $url ) . '%' );
		}

		return $query;
	}

	/**
	 * Filter log by referral.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $ref   Error referral string.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeReferral( $query, $ref ) {
		if ( ! empty( $ref ) ) {
			return $query->where( 'ref', '=', esc_sql( $ref ) );
		}

		return $query;
	}

	/**
	 * Filter log by User Agent.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $ua    Error user agent string.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeUserAgent( $query, $ua ) {
		if ( ! empty( $ua ) ) {
			return $query->where( 'ua', '=', esc_sql( $ua ) );
		}

		return $query;
	}

	/**
	 * Filter log by user's IP.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $ip    Error user IP string.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeIP( $query, $ip ) {
		if ( ! empty( $ip ) ) {
			return $query->where( 'ip', '=', esc_sql( $ip ) );
		}

		return $query;
	}

	/**
	 * Filter log by the date.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $date  Error log time.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeDate( $query, $date ) {
		if ( ! empty( $date ) ) {
			return $query->where( 'date', '=', esc_sql( $date ) );
		}

		return $query;
	}

	/**
	 * Filter log by the date less than.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $date  Error log time.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeDateTo( $query, $date ) {
		if ( ! empty( $date ) ) {
			return $query->where( 'date', '<=', esc_sql( $date ) );
		}

		return $query;
	}

	/**
	 * Filter log by the date starting from.
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $date  Error log time.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeDateFrom( $query, $date ) {
		if ( ! empty( $date ) ) {
			return $query->where( 'date', '>=', esc_sql( $date ) );
		}

		return $query;
	}

	/**
	 * Filter log by the date between two periods.
	 *
	 * This is basically the combination of dateFrom and dateTo
	 *
	 * @param Builder $query Query builder instance.
	 * @param string  $from  Error log time from.
	 * @param string  $to    Error log time to.
	 *
	 * @since 4.0.0
	 *
	 * @return Builder
	 */
	public function scopeDateBetween( $query, $from, $to ) {
		if ( ! empty( $from ) && ! empty( $to ) ) {
			return $query
				->whereDate( 'date', '<=', $to )
				->whereDate( 'date', '>=', $from );
		}

		return $query;
	}
}