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

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Data;

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
		add_action( 'dd404_admin_settings_info_form_content', array( $this, 'info_content' ) );

		add_action( 'dd404_admin_notices', array( $this, 'show_settings_notice' ) );
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
			'user_name'   => 'Joel',
			'menu_config' => array(
				'current' => $this->get_current_tab(),
				'items'   => $this->get_settings_tabs(),
			),
		);

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_before_admin_pages_settings_render' );

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
		$args = array(
			'types' => Data\Redirect::redirect_types(),
		);

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
	public function info_content() {
		// Arguments.
		$args = array();

		// Admin settings template.
		$this->render( 'settings/info', $args );

		/**
		 * Action hook to run something after rendering email settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_after_admin_pages_info_settings_render' );
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
			'redirect' => array(
				'title' => __( 'Redirect', '404-to-301' ),
				'icon'  => 'randomize',
			),
			'logs'     => array(
				'title' => __( 'Logs', '404-to-301' ),
				'icon'  => 'media-default',
			),
			'email'    => array(
				'title' => __( 'Email', '404-to-301' ),
				'icon'  => 'email-alt',
			),
			'general'  => array(
				'title' => __( 'General', '404-to-301' ),
				'icon'  => 'admin-generic',
			),
			'info'     => array(
				'title' => __( 'Info', '404-to-301' ),
				'icon'  => 'info',
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
	 * @since  4.0
	 *
	 * @return array
	 */
	public function show_submit() {
		$hidden = array( 'info' );

		$show = ! in_array( $this->get_current_tab(), $hidden, true );

		/**
		 * Action hook to run something after rendering settings page.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd404_admin_settings_show_submit', $show );
	}

	/**
	 * Register the sub menu for the admin settings.
	 *
	 * @param string $page Current page.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function show_settings_notice( $page ) {
		if ( 'settings' === $page ) {
			// Get the errors.
			$errors = get_settings_errors();

			if ( empty( $errors ) ) {
				return;
			}

			foreach ( $errors as $details ) {
				$this->render(
					'components/notices/notice',
					array(
						'type'    => $details['type'],
						'content' => $details['message'],
						'options' => array( 'id' => $details['code'] ),
					),
					false
				);
			}
		}
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
	public function get_current_tab( $default = 'redirect' ) {
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
