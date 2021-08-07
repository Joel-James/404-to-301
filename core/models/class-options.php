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

/**
 * Class Tables.
 *
 * @package DuckDev\Redirect\Models
 */
class Options extends Model {

	/**
	 * Current table name.
	 *
	 * @var string $table Table name.
	 *
	 * @since 4.0
	 */
	public $name = 'options';

	/**
	 * Set up the plugin and register all hooks.
	 *
	 * Pro version features and not initialized yet, so do not
	 * execute something on these hooks if you are checking for
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
