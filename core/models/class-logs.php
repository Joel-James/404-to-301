<?php
/**
 * The error logs model class.
 *
 * This class handles the database queries for error logs
 * management.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Endpoint
 * @since      4.0.0
 * @subpackage Settings
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Utils\Abstracts\Model;

/**
 * Class Tables.
 *
 * @package DuckDev\DD4T3\Models
 */
class Logs extends Model {

	/**
	 * Current model table name.
	 *
	 * @var string $table Table name.
	 *
	 * @since 4.0
	 */
	protected $table = '404-to-301';

	/**
	 * Field names of the table.
	 *
	 * @var string[] $fields
	 *
	 * @since 4.0.0
	 */
	protected $fields = array(
		'id'       => '%d',
		'date'     => '%s',
		'url'      => '%s',
		'ref'      => '%s',
		'ip'       => '%s',
		'ua'       => '%s',
		'redirect' => '%s', // @deprecated.
		'status'   => '%s',
	);

	/**
	 * Setup the plugin and register all hooks.
	 *
	 * Pro version features and not initialized yet, so do not
	 * execute something on this hooks if you are checking for
	 * Pro version.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public function get_list() {
		$list = array();

		/**
		 * Action hook to trigger after initializing all core actions.
		 *
		 * You still need to check if it Pro version or Free.
		 *
		 * @since 1.8.0
		 */
		return apply_filters( '404_to_301_model_logs_get_list', $list );
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
	public function create( $data = array() ) {
		// Can not continue if url is empty.
		if ( empty( $data['url'] ) ) {
			return false;
		}

		return $this->insert( $data );
	}
}
