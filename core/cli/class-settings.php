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
 * @since   4.0.0
 * @extends Command
 * @package DuckDev\Redirect\CLI
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
		$key   = $this->get_arg( $extra, 'key' );
		$value = $this->get_arg( $extra, 'value' );

		// If updating one setting.
		if ( 'set' === $action && $key && $value ) {
			$this->set_setting( $key, $value );
		} elseif ( 'get' === $action ) {
			if ( $key ) {
				// Get single setting.
				$this->get_setting( $key );
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
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 *
	 * @since  4.0.0
	 * @access private
	 * @uses   dd4t3_settings()
	 *
	 * @return void
	 */
	private function set_setting( $key, $value ) {
		if ( dd4t3_settings()->set( $key, $value ) ) {
			$this->success( __( 'Setting updated successfully!', '404-to-301' ) );
		} else {
			$this->error( __( 'Setting update failed.', '404-to-301' ) );
		}
	}

	/**
	 * Get a single setting value and display it.
	 *
	 * @param string $key Setting key.
	 *
	 * @since  4.0.0
	 * @access private
	 * @uses   dd4t3_settings()
	 *
	 * @return void
	 */
	private function get_setting( $key ) {
		// Get the setting value.
		$value = dd4t3_settings()->get( $key, false, $valid );
		// Display result.
		$valid ? $this->show( $value ) : $this->error( __( 'Invalid settings.', '404-to-301' ) );
	}

	/**
	 * Get the whole plugin settings and display it.
	 *
	 * @since  4.0.0
	 * @access private
	 * @uses   dd4t3_settings()
	 *
	 * @return void
	 */
	private function get_all_settings() {
		$this->maybe_as_table(
			dd4t3_settings()->all(),
			array( 'module', 'values' )
		);
	}
}
