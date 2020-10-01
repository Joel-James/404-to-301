<?php
/**
 * The core plugin class.
 *
 * @link    http://premium.wpmudev.org
 * @since   1.8.0
 *
 * @author  Joel James <joel@incsub.com>
 * @package DuckDev\Redirect
 */

namespace DuckDev\DD4T3\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\DD4T3\Abstracts\Model;

/**
 * Class Core.
 *
 * @package DuckDev\Redirect\Core
 */
class Tables extends Model {

	const LOG_TABLE = '404_to_301';

	const OPTIONS_TABLE = '404_to_301_options';

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
		/**
		 * Action hook to trigger after initializing all core actions.
		 *
		 * You still need to check if it Pro version or Free.
		 *
		 * @since 1.8.0
		 */
		do_action( 'wpmudev_vids_after_core_init' );
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
	public function logTable() {
		$query = "CREATE TABLE $table (
            id BIGINT NOT NULL AUTO_INCREMENT,
            date DATETIME NOT NULL,
            url VARCHAR(512) NOT NULL,
            ref VARCHAR(512) NOT NULL default '',
            ip VARCHAR(40) NOT NULL default '',
            ua VARCHAR(512) NOT NULL default '',
            redirect VARCHAR(512) NULL default '',
			options LONGTEXT,
			status BIGINT NOT NULL default 1,
            PRIMARY KEY  (id)
        );";

		/**
		 * Action hook to trigger after initializing all core actions.
		 *
		 * You still need to check if it Pro version or Free.
		 *
		 * @since 1.8.0
		 */
		do_action( 'wpmudev_vids_after_core_init' );
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
	public function optionTable() {
		/**
		 * Action hook to trigger after initializing all core actions.
		 *
		 * You still need to check if it Pro version or Free.
		 *
		 * @since 1.8.0
		 */
		do_action( 'wpmudev_vids_after_core_init' );
	}
}
