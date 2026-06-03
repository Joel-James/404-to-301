<?php
/**
 * Plugin row links on the Plugins screen.
 *
 * Adds the "Logs", "Redirects" and "Settings" action links and a
 * "Documentation" + "Support" row-meta link to the plugin's entry in
 * `wp-admin/plugins.php`, so admins can jump straight to the plugin
 * pages without leaving the list table.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Plugin;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Links
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Admin
 */
class Links extends Singleton {

	/**
	 * Hook the plugin row filters.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_filter( 'plugin_action_links_' . D404_BASE_NAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'row_meta' ), 10, 2 );
	}

	/**
	 * Prepend Logs / Redirects / Settings links to the plugin actions.
	 *
	 * @since 4.0.0
	 *
	 * @param array $links Existing action links.
	 *
	 * @return array
	 */
	public function action_links( $links ): array {
		$links = is_array( $links ) ? $links : array();

		$ours = array(
			sprintf( '<a href="%s">%s</a>', esc_url( Plugin::get_url( 'settings' ) ), esc_html__( 'Settings', '404-to-301' ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( Plugin::get_url( 'logs' ) ), esc_html__( 'Logs', '404-to-301' ) ),
			sprintf( '<a href="%s">%s</a>', esc_url( Plugin::get_url( 'redirects' ) ), esc_html__( 'Redirects', '404-to-301' ) ),
		);

		return array_merge( $ours, $links );
	}

	/**
	 * Append documentation and support links to the plugin's row meta.
	 *
	 * @since 4.0.0
	 *
	 * @param string[] $meta Existing row-meta links.
	 * @param string   $file Plugin basename of the row currently being rendered.
	 *
	 * @return array
	 */
	public function row_meta( $meta, $file ): array {
		$meta = is_array( $meta ) ? $meta : array();

		if ( D404_BASE_NAME === $file ) {
			$meta['docs'] = sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
				'https://docs.duckdev.com/404-to-301/',
				esc_html__( 'Documentation', '404-to-301' )
			);

			$meta['support'] = sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
				'https://wordpress.org/support/plugin/404-to-301/',
				esc_html__( 'Support', '404-to-301' )
			);
		}

		return $meta;
	}
}
