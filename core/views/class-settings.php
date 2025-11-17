<?php
/**
 * The plugin settings page view class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Settings
 */

namespace DuckDev\FourNotFour\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\FourNotFour\Data;

/**
 * Class Settings
 *
 * @extends View
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Views
 */
class Settings extends View {

	/**
	 * Register all hooks for the settings UI.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		// Render settings page templates.
		add_action( '404_to_301_admin_settings_info_form_content', array( $this, 'info_content' ) );
		add_action( '404_to_301_admin_settings_logs_form_content', array( $this, 'logs_content' ) );
		add_action( '404_to_301_admin_settings_general_form_content', array( $this, 'general_content' ) );
		add_action( '404_to_301_admin_settings_redirects_form_content', array( $this, 'redirects_content' ) );
		add_action( '404_to_301_admin_settings_notifications_form_content', array( $this, 'notifications_content' ) );

		// Render notice for settings page.
		add_action( '404_to_301_admin_notices', array( $this, 'show_settings_notice' ) );
	}

	/**
	 * Render base content template for settings.
	 *
	 * Actual contents are rendered as separate tabs.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function base_content() {
		// Current user.
		$user = wp_get_current_user();

		// Render template.
		$this->render(
			'settings',
			array(
				'page'        => $this->get_current_tab(),
				'user_name'   => empty( $user->display_name ) ? $user->user_login : $user->display_name,
				'menu_config' => array(
					'current' => $this->get_current_tab(),
					'items'   => $this->get_settings_tabs(),
				),
			)
		);
	}

	/**
	 * Render general settings template.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function general_content() {
		// Render template.
		$this->render(
			'settings/general',
			array()
		);

		/**
		 * Action hook to run something after rendering general settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_general_settings_render' );
	}

	/**
	 * Render redirect settings template.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function redirects_content() {
		// Render template.
		$this->render(
			'settings/redirects',
			array(
				'types' => Data::redirect_types(),
			)
		);

		/**
		 * Action hook to run something after rendering redirect settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_redirect_settings_render' );
	}

	/**
	 * Render logs settings template.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function logs_content() {
		// Render template.
		$this->render(
			'settings/logs',
			array()
		);

		/**
		 * Action hook to run something after rendering logs settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_logs_settings_render' );
	}

	/**
	 * Render email settings template.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function notifications_content() {
		// Render template.
		$this->render(
			'settings/notifications',
			array()
		);

		/**
		 * Action hook to run something after rendering email settings page.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_notifications_settings_render' );
	}

	/**
	 * Render info section template.
	 *
	 * This section contains the plugin help and support details,
	 * addons and documentation links.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function info_content() {
		// Render template.
		$this->render(
			'settings/info',
			array()
		);

		/**
		 * Action hook to run something after rendering info section.
		 *
		 * @since 4.0.0
		 */
		do_action( '404_to_301_after_info_settings_render' );
	}

	/**
	 * Get plugin settings tabs configuration.
	 *
	 * Other plugins should hook into 404_to_301_settings_page_get_tabs filter
	 * to add new tab item.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_settings_tabs() {
		$tabs = array(
			'redirects'     => array(
				'title' => __( 'Redirect', '404-to-301' ),
				'icon'  => 'randomize',
			),
			'logs'          => array(
				'title' => __( 'Logs', '404-to-301' ),
				'icon'  => 'media-default',
			),
			'notifications' => array(
				'title' => __( 'Notifications', '404-to-301' ),
				'icon'  => 'email-alt',
			),
			'general'       => array(
				'title' => __( 'General', '404-to-301' ),
				'icon'  => 'admin-generic',
			),
			'info'          => array(
				'title' => __( 'Info', '404-to-301' ),
				'icon'  => 'info',
			),
		);

		/**
		 * Filter to add or remove settings tabs.
		 *
		 * @param array $tabs Tabs config.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_settings_page_get_tabs', $tabs );
	}

	/**
	 * Check if we need to show submit button section.
	 *
	 * Only settings forms require submit buttons.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function show_submit() {
		$hidden = array( 'info' );

		$show = ! in_array( $this->get_current_tab(), $hidden, true );

		/**
		 * Filter to modify settings submit section hidden status.
		 *
		 * @param bool  $show   Should show.
		 * @param array $hidden Hidden items.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_admin_settings_show_submit', $show, $hidden );
	}

	/**
	 * Render settings update notices.
	 *
	 * Show success/error notices after processing the form.
	 *
	 * @param string $page Current page.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function show_settings_notice( $page ) {
		// Only on settings page.
		if ( 'settings' === $page ) {
			// Get the errors.
			$errors = get_settings_errors();

			// No need to continue if no messages.
			if ( empty( $errors ) ) {
				return;
			}

			// Render each message as notice.
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
	 * Get currently active tab name.
	 *
	 * This is required to show current tab content, add
	 * active class to tab etc.
	 *
	 * @param string $default Default tab.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_current_tab( $default = 'redirects' ) {
		// Get tab value.
		$tab = $this->get_param( 'tab', $default );

		// Get allowed tabs.
		$tabs = array_keys( $this->get_settings_tabs() );

		// Make sure it's not empty.
		$tab = ! in_array( $tab, $tabs, true ) ? $default : $tab;

		/**
		 * Filter to modify active tabs logic.
		 *
		 * @param string $tab  Current tab.
		 * @param array  $tabs Allowed tabs.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_admin_settings_page_get_current_tab', $tab );
	}
}
