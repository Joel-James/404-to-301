<?php

namespace DuckDev\WP404\Controllers\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The general functionality of the admin side of plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class General extends Base {

	/**
	 * Initilize the class by registering the hooks.
	 *
	 * @since 4.0.0
	 */
	public function init() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
	}

	/**
	 * Registering i4t3 options.
	 *
	 * This function is used to register all settings options to the db using
	 * WordPress settings API.
	 *
	 * @since  2.0.0
	 * @access public
	 * @uses   hooks  register_setting Hook to register i4t3 options in db.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'404_to_301_settings',
			'404_to_301_settings'
		);
	}

	/**
	 * Action links for plugins listing page.
	 *
	 * Add quick links to plugin settings page, error listing page
	 * from the plugins listing page.
	 *
	 * @param array  $links Links array.
	 * @param string $file  File name.
	 *
	 * @return array
	 */
	public function action_links( $links, $file ) {
		// Plugin base name.
		$plugin_file = basename( '404-to-301.php' );

		// Only when it is our plugin.
		if ( basename( $file ) === $plugin_file ) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page=404-to-301' ) . '">' . __( 'Settings', '404-to-301' ) . '</a>';
			$settings_link .= ' | <a href="' . admin_url( 'admin.php?page=jj4t3-logs' ) . '">' . __( 'Logs', '404-to-301' ) . '</a>';

			// Add quick links to plugins listing page.
			array_unshift( $links, $settings_link );
		}

		return $links;
	}
}
