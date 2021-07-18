<?php
/**
 * The plugin pages view class.
 *
 * This class handles the admin pages views for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Pages
 */

namespace DuckDev\Redirect\Views;

use DuckDev\Redirect\Utils\Abstracts\View;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Settings extends View {

	/**
	 * Register all hooks for the settings UI.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		// Render settings pages.
		add_action( 'dd404_admin_settings_general_form_content', array( $this, 'general_content' ) );
		add_action( 'dd404_admin_settings_redirect_form_content', array( $this, 'redirect_content' ) );
		add_action( 'dd404_admin_settings_logs_form_content', array( $this, 'logs_content' ) );
		add_action( 'dd404_admin_settings_email_form_content', array( $this, 'email_content' ) );
		add_action( 'dd404_after_admin_pages_settings_render', array( $this, 'render_templates' ) );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function base_content() {
		// Arguments.
		$args = array(
			'page'        => $this->get_current_tab(),
			'menu_config' => array(
				'current' => $this->get_current_tab(),
				'items'   => $this->get_settings_tabs(),
			),
		);

		// Admin settings template.
		$this->render( 'settings', $args );

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_settings_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function general_content() {
		// Arguments.
		$args = array();

		// Admin settings template.
		$this->render( 'settings/general', $args );

		/**
		 * Action hook to run something after rendering general settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_general_settings_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function redirect_content() {
		// Arguments.
		$args = array();

		// Admin settings template.
		$this->render( 'settings/redirect', $args );

		/**
		 * Action hook to run something after rendering redirect settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_redirect_settings_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function logs_content() {
		// Arguments.
		$args = array();

		// Admin settings template.
		$this->render( 'settings/logs', $args );

		/**
		 * Action hook to run something after rendering logs settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_logs_settings_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function email_content() {
		// Arguments.
		$args = array();

		// Admin settings template.
		$this->render( 'settings/email', $args );

		/**
		 * Action hook to run something after rendering email settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_email_settings_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function render_templates() {
		$templates = array( 'form-submit' );

		foreach ( $templates as $template ) {
			$this->render( "components/vue/{$template}" );
		}

		/**
		 * Action hook to run something after rendering email settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_after_templates_render' );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public function get_settings_tabs() {
		$tabs = array(
			'general'  => array(
				'title' => __( 'General', '404-to-301' ),
				'icon'  => 'admin-settings',
				'url'   => add_query_arg( 'tab', 'general' ),
			),
			'redirect' => array(
				'title' => __( 'Redirect', '404-to-301' ),
				'icon'  => 'randomize',
				'url'   => add_query_arg( 'tab', 'redirect' ),
			),
			'logs'     => array(
				'title' => __( 'Logs', '404-to-301' ),
				'icon'  => 'media-default',
				'url'   => add_query_arg( 'tab', 'logs' ),
			),
			'email'    => array(
				'title' => __( 'Email', '404-to-301' ),
				'icon'  => 'email-alt',
				'url'   => add_query_arg( 'tab', 'email' ),
			),
		);

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_admin_settings_page_get_tabs', $tabs );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @param string $default Default tab.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public function get_current_tab( $default = 'general' ) {
		// Get tab value.
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );

		// Get allowed tabs.
		$tabs = array_keys( $this->get_settings_tabs() );

		// Make sure it's not empty.
		$tab = empty( $tab ) || ! in_array( $tab, $tabs, true ) ? $default : $tab;

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_admin_settings_page_get_current_tab', $tab );
	}
}
