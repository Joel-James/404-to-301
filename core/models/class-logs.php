<?php
/**
 * The error logs model class.
 *
 * This class handles the database queries for error logs.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Model
 * @subpackage Logs
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\QueryBuilder\Query;

/**
 * Class Logs.
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Models
 * @extends Model
 */
class Logs extends Model {

	/**
	 * Get log data by ID.
	 *
	 * Return the log data from using the ID.
	 *
	 * @param int $id Log ID.
	 *
	 * @since 4.0.0
	 *
	 * @throws \Exception Exception.
	 * @return mixed|false
	 */
	public function get( $id ) {
		return $this->remember(
			"log_$id",
			function () use ( $id ) {
				return Query::init( 'logs_get' )
					->from( $this->table_name( 'logs' ) )
					->find( intval( $id ) );
			}
		);
	}

	/**
	 * Setup the plugin and register all hooks.
	 *
	 * Pro version features and not initialized yet, so do not
	 * execute something on this hooks if you are checking for
	 * Pro version.
	 *
	 * @param array $data Data.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public function create( array $data ) {
		// Can not continue if url is empty.
		if ( empty( $data['url'] ) ) {
			return false;
		}

		return $this->insert( $data, 'logs' );
	}
}
