<?php
/**
 * The plugin pages view class.
 *
 * This class handles the admin pages views for the plugin.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Pages
 */

namespace DuckDev\Redirect\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Reviews\Notice;
use DuckDev\Redirect\Plugin;

/**
 * Class Admin
 *
 * @since   4.0.0
 * @extends View
 * @package DuckDev\Redirect\Views
 */
class Admin extends View {

	/**
	 * Register all hooks for admin view.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		// Add screen options.
		add_action( 'current_screen', array( $this, 'screen_options' ) );

		// Setup action links.
		add_filter( 'plugin_row_meta', array( $this, 'row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( DD4T3_FILE ), array( $this, 'action_links' ) );

		// Admin notices.
		add_action( 'dd4t3_admin_notices', array( $this, 'show_review_notice' ) );
		add_action( 'dd4t3_admin_notices', array( $this, 'upgrade_notice' ) );
	}

	/**
	 * Show review notice on our plugin pages.
	 *
	 * Ask for a wp.org review if plugin is being in use for more than
	 * 1 week (7 days).
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function show_review_notice() {
		// Setup review notice.
		Notice::get(
			'404-to-301',
			'404 to 301',
			array( 'classes' => array( 'duckdev-notice' ) )
		)->render(); // Render notice.
	}

	/**
	 * Show upgrade in progress notice on our plugin pages.
	 *
	 * Let the admins know that we are upgrading database in background.
	 * This won't affect our plugin functionality.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade_notice() {
		$this->render(
			'components/notices/notice',
			array(
				'type'    => 'info',
				'content' => sprintf(
				// translators: %s plugin name.
					__( '<strong>%s</strong> is upgrading database in background.', '404-to-301' ),
					Plugin::name()
				),
			),
			false
		);
	}

	/**
	 * Register screen options section.
	 *
	 * Show a help section for our plugin's pages. Link to plugin
	 * page and documentation.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function screen_options() {
		$screen = get_current_screen();

		// Only on our pages.
		if ( ! empty( $screen->id ) && in_array( $screen->id, Plugin::screens(), true ) ) {
			$items = array(
				'overview' => __( 'Overview', '404-to-301' ),
				'help'     => __( 'Help & Support', '404-to-301' ),
			);

			// Set items.
			foreach ( $items as $id => $label ) {
				$screen->add_help_tab(
					array(
						'id'      => $id,
						'title'   => $label,
						'content' => $this->get_render(
							"components/screen-options/tab-$id"
						),
					)
				);
			}

			// Set sidebar.
			$screen->set_help_sidebar(
				$this->get_render(
					'components/screen-options/sidebar'
				)
			);
		}
	}

	/**
	 * Action links for plugins listing page.
	 *
	 * Add quick links to plugin settings page, error listing page
	 * from the plugins listing page.
	 *
	 * @param array $links Links array.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function action_links( $links ) {
		// Add settings and log links.
		array_unshift( $links, '<a href="' . esc_url( Plugin::get_url( 'logs' ) ) . '">' . __( 'Logs', '404-to-301' ) . '</a>' );
		array_unshift( $links, '<a href="' . esc_url( Plugin::get_url( 'settings' ) ) . '">' . __( 'Settings', '404-to-301' ) . '</a>' );

		return $links;
	}

	/**
	 * Plugins row meta links.
	 *
	 * Add plugin support and documentation links.
	 *
	 * @param string[] $meta An array of the plugin's metadata.
	 * @param string   $file Path to the plugin file.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function row_meta( $meta, $file ) {
		// Add only for our plugin.
		if ( plugin_basename( DD4T3_FILE ) === $file ) {
			$meta['docs'] = '<a href="https://duckdev.com/docs/404-to-301/?utm_source=dd4t3&utm_medium=plugin&utm_campaign=plugins_row_meta" target="_blank">' . __( 'Documentation', '404-to-301' ) . '</a>';
			$meta['home'] = '<a href="https://wordpress.org/support/plugin/404-to-301/" target="_blank">' . __( 'Support', '404-to-301' ) . '</a>';
		}

		return $meta;
	}
}
