<?php

namespace DuckDev404\Core\Controllers\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev404\Core\Utils\Abstracts\Base;

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
			'i4t3_gnrl_options',
			'i4t3_gnrl_options'
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
