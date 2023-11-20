<?php
/**
 * The plugin pages view class.
 *
 * This class handles the admin pages views for the plugin.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    View
 * @subpackage Pages
 */

namespace RedirectPress\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Reviews\Notice;
use RedirectPress\Plugin;
use RedirectPress\Models\Logs;
use RedirectPress\Database\Upgrader;

/**
 * Class Admin
 *
 * @since   4.0.0
 * @extends View
 * @package RedirectPress\Views
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
		add_filter( 'plugin_action_links_' . plugin_basename( REDIRECTPRESS_FILE ), array( $this, 'action_links' ) );

		// Admin notices.
		add_action( 'redirectpress_admin_notices', array( $this, 'show_review_notice' ) );

		// Add site health info.
		add_filter( 'site_status_tests', array( $this, 'site_health_tests' ) );
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
	 * @since  3.0.0
	 * @access public
	 *
	 * @param array $links Links array.
	 *
	 * @return array
	 */
	public function action_links( $links ) {
		// Add our links.
		array_unshift( $links, '<a href="' . esc_url( Plugin::get_url( 'settings' ) ) . '">' . __( 'Settings', '404-to-301' ) . '</a>' );
		array_unshift( $links, '<a href="' . esc_url( Plugin::get_url( 'logs' ) ) . '">' . __( 'Logs', '404-to-301' ) . '</a>' );
		array_unshift( $links, '<a href="' . esc_url( Plugin::get_url( 'redirects' ) ) . '">' . __( 'Redirects', '404-to-301' ) . '</a>' );

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
		if ( plugin_basename( REDIRECTPRESS_FILE ) === $file ) {
			$meta['docs'] = '<a href="https://duckdev.com/docs/404-to-301/?utm_source=redirectpress&utm_medium=plugin&utm_campaign=plugins_row_meta" target="_blank"><span class="dashicons dashicons-book" style="font-size:14px;line-height:1.3;"></span>' . __( 'Documentation', '404-to-301' ) . '</a>';
			$meta['home'] = '<a href="https://wordpress.org/support/plugin/404-to-301/" target="_blank"><span class="dashicons dashicons-sos" style="font-size:14px;line-height:1.3;"></span>' . __( 'Support', '404-to-301' ) . '</a>';
		}

		return $meta;
	}

	/**
	 * Add error status to the site health test.
	 *
	 * If no 404 errors found, site should be considered as clean.
	 * NOTE: Logs will be empty if logging is disabled.
	 *
	 * @since 4.0.0
	 *
	 * @param array $tests Test items.
	 *
	 * @return array
	 */
	public function site_health_tests( array $tests ) {
		// Add our custom info.
		$tests['direct']['404_to_301'] = array(
			'label' => __( '404 Errors', '404-to-301' ),
			'test'  => array( $this, 'site_health_info' ),
		);

		return $tests;
	}

	/**
	 * Site health info section data.
	 *
	 * Show information about 404 errors on the site health test
	 * section.
	 * Having 404 error logs means they have broken links.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function site_health_info() {
		// If there are error logs found.
		$has_logs = Logs::instance()->has_items();

		return array(
			'label'       => $has_logs ? __( 'One or more 404 errors are found', '404-to-301' ) : __( 'No 404 errors', '404-to-301' ),
			'status'      => $has_logs ? 'recommended' : 'good',
			'badge'       => array(
				'label' => __( 'Optimization', '404-to-301' ),
				'color' => $has_logs ? 'orange' : 'green',
			),
			'description' => sprintf(
				'<p>%s</p>',
				$has_logs ? __( 'There are 404 errors on your website. You should fix them by redirecting it.', '404-to-301' ) : __( 'There are no 404 errors on your website yet.', '404-to-301' )
			),
			'actions'     => $has_logs ? sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( Plugin::get_url( 'logs' ) ),
				__( 'Manage Logs', '404-to-301' )
			) : sprintf(
				'<p><a href="%s">%s</a></p>',
				esc_url( Plugin::get_url( 'redirects' ) ),
				__( 'Manage Redirects', '404-to-301' )
			),
			'test'        => '404_to_301',
		);
	}
}
