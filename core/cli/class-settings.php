<?php
/**
 * The plugin settings command class.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    CLI
 * @subpackage Settings
 */

namespace DuckDev\Redirect\CLI;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Settings
 *
 * Plugin settings CLI command.
 *
 * @package DuckDev\Redirect\CLI
 * @since   4.0.0
 */
class Settings extends Command {

	/**
	 * Manage plugin settings.
	 *
	 * Get or set plugin setting values.
	 *
	 * @param array $args  Command arguments.
	 * @param array $extra Extra options.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function command( $args, $extra ) {
		// Action.
		$action = $args[0];
		// Setting keys.
		$module = $this->get_arg( $extra, 'module' );
		$key    = $this->get_arg( $extra, 'key' );
		$value  = $this->get_arg( $extra, 'value' );

		// If updating one setting.
		if ( 'set' === $action && $module && $key && $value ) {
			$this->set_setting( $key, $value, $module );
		} elseif ( 'get' === $action ) {
			if ( $module && $key ) {
				// Get single setting.
				$this->get_setting( $module, $key );
			} elseif ( $module ) {
				// Get module setting.
				$this->get_module( $module );
			} else {
				// Get the whole settings.
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
			$this->success( __( 'Setting updated successfully!', '404-to-301' ) );
		} else {
			$this->error( __( 'Setting update failed.', '404-to-301' ) );
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
		$valid ? $this->show( $value ) : $this->error( __( 'Invalid settings.', '404-to-301' ) );
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
		$valid ? $this->maybe_as_table( $values ) : $this->error( __( 'Invalid settings.', '404-to-301' ) );
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
