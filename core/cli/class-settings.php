<?php
/**
 * The core plugin class.
 *
 * This is the main class that initialize the entire plugin functionality.
 * Only one instance of this class be created.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Core
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_CLI;
use WP_CLI\Utils;

/**
 * Class CLI
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Settings extends CLI {

	/**
	 * Get the plugin setttings.
	 *
	 * ## OPTIONS
	 *
	 * <action>
	 * : The setting action type (get or set).
	 *
	 * [--module=<value>]
	 * : The setting key name.
	 *
	 * [--key=<value>]
	 * : The setting key name.
	 *
	 * [--value=<value>]
	 * : The value to set.
	 *
	 * ## EXAMPLES
	 *
	 *     wp 404-to-301 setting <action> --key=<value> --value=<value> --module=<value>
	 *
	 * @param array $args  Command arguments.
	 * @param array $extra Extra options.
	 *
	 * @return void
	 */
	public function settings( $args, $extra ) {
		$action = $args[0];
		$module = $this->get_arg( $extra, 'module' );
		$key    = $this->get_arg( $extra, 'key' );
		$value  = $this->get_arg( $extra, 'value' );

		// If updating one setting.
		if ( 'set' === $action && $module && $key && $value ) {
			$this->set_setting( $key, $value, $module );
		} elseif ( 'get' === $action ) {
			if ( $module && $key ) {
				$this->get_setting( $module, $key );
			} elseif ( $module ) {
				$this->get_module( $module );
			} else {
				$this->get_all_settings();
			}
		} else {
			$this->error();
		}
	}

	/**
	 * Update a single setting value.
	 *
	 * @param string $key    Setting key.
	 * @param mixed  $value  Setting value.
	 * @param string $module Setting module.
	 *
	 * @since  4.0.0
	 * @access private
	 * @uses   dd404_settings()
	 *
	 * @return void
	 */
	private function set_setting( $key, $value, $module ) {
		if ( dd404_settings()->update( $key, $value, $module ) ) {
			$this->success( 'Setting updated successfully!' );
		} else {
			$this->error( 'Setting update failed.' );
		}
	}

	/**
	 * Get a single setting value and display it.
	 *
	 * @param string $module Setting module.
	 * @param string $key    Setting key.
	 *
	 * @since  4.0.0
	 * @access private
	 * @uses   dd404_settings()
	 *
	 * @return void
	 */
	private function get_setting( $module, $key ) {
		// Get the setting value.
		$value = dd404_settings()->get( $key, $module, false, $valid );
		// Display result.
		$valid ? $this->show( $value ) : $this->error( 'Invalid setting.' );
	}

	/**
	 * Get a module settings values and display.
	 *
	 * @param string $module Setting module.
	 *
	 * @since  4.0.0
	 * @access private
	 * @uses   dd404_settings()
	 *
	 * @return void
	 */
	private function get_module( $module ) {
		// Get the module values.
		$values = dd404_settings()->get_module( $module, array(), $valid );
		// Display result.
		$valid ? $this->maybe_as_table( $values ) : $this->error( 'Invalid settings.' );
	}

	/**
	 * Get the whole plugin settings and display it.
	 *
	 * @since  4.0.0
	 * @access private
	 * @uses   dd404_settings()
	 *
	 * @return void
	 */
	private function get_all_settings() {
		$this->maybe_as_table(
			dd404_settings()->get_settings(),
			array( 'module', 'values' )
		);
	}
}
