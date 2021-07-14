<?php
/**
 * The database controller.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @since      4.0.0
 * @subpackage DB
 */

namespace DuckDev\Redirect\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Models;
use DuckDev\Redirect\Utils\Abstracts\Base;

/**
 * Class DB
 *
 * @package DuckDev\Redirect\Controllers
 */
class DB extends Base {

	/**
	 * Initialize the class.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function init() {

	}

	/**
	 * Setup the plugin and register all hooks.
	 *
	 * Pro version features and not initialized yet, so do not
	 * execute something on this hooks if you are checking for
	 * Pro version.
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public function create() {
		// Get the create schemas.
		$tables = array(
			Models\Logs::instance(),
			Models\Options::instance(),
		);

		// Make sure dbDelta is available to handle DB upgrades properly.
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$done = array();

		foreach ( $tables as $table ) {
			if ( ! empty( $table->schema() ) ) {
				// Update or create table in database.
				$result = dbDelta( $table->schema() );

				if ( ! empty( $result ) ) {
					if ( $table->table_ready() ) {
						$done[] = $table->table_name();
					}
				}
			}
		}

		if ( ! empty( $done ) ) {
			$ready = Settings::instance()->get( 'tables', 'misc', array() );
			$ready = array_merge( $done, $ready );
			// Mark the done tables as ready.
			Settings::instance()->update(
				'tables',
				array_unique( $ready ),
				'misc'
			);
		}

		/**
		 * Action hook to trigger after creating tables.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_db_after_table_create' );
	}
}
