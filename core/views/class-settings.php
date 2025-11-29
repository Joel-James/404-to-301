<?php
/**
 * The plugin settings page view class.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    View
 * @subpackage Settings
 */

namespace DuckDev\FourNotFour\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\FourNotFour\Data;
use DuckDev\FourNotFour\Plugin;
use DuckDev\FourNotFour\Utils\Base;

/**
 * Class Settings
 *
 * @extends View
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Views
 */
class Settings extends Base {

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
		View::render(
			'settings-react',
			array(
				'page'        => $this->get_current_tab(),
				'user_name'   => empty( $user->display_name ) ? $user->user_login : $user->display_name,
				'menu_config' => array(
					'base_url'    => Plugin::get_url(),
					'current_tab' => $this->get_current_tab(),
					'tab_items'   => $this->get_settings_tabs(),
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
		View::render(
			'settings/tab-general',
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
		View::render(
			'settings/tab-redirects',
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
		View::render(
			'settings/tab-logs',
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
		View::render(
			'settings/tab-notifications',
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
		View::render(
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
	public function get_settings_tabs(): array {
		$tabs = array(
			'redirects'     => array(
				'label' => __( 'Redirects', '404-to-301' ),
				'icon'  => 'dashicons-redo',
			),
			'logs'          => array(
				'label' => __( 'Logs', '404-to-301' ),
				'icon'  => 'dashicons-media-default',
			),
			'notifications' => array(
				'label' => __( 'Notifications', '404-to-301' ),
				'icon'  => 'dashicons-email-alt',
			),
			'general'       => array(
				'label' => __( 'General', '404-to-301' ),
				'icon'  => 'dashicons-admin-settings',
			),
		);

		/**
		 * Filter to add or remove settings tabs.
		 *
		 * @since 4.0.0
		 *
		 * @param array $tabs Tabs config.
		 *
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
		 * @since 4.0.0
		 *
		 * @param array $hidden Hidden items.
		 *
		 * @param bool  $show   Should show.
		 */
		return apply_filters( '404_to_301_admin_settings_show_submit', $show, $hidden );
	}

	/**
	 * Render settings update notices.
	 *
	 * Show success/error notices after processing the form.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $page Current page.
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
				View::render(
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
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $default Default tab.
	 *
	 * @return string
	 */
	public function get_current_tab( string $default = 'redirects' ): string {
		// Get tab value.
		$tab = View::get_param( 'tab', $default );

		// Get allowed tabs.
		$tabs = array_keys( $this->get_settings_tabs() );

		// Make sure it's not empty.
		$tab = ! in_array( $tab, $tabs, true ) ? $default : $tab;

		/**
		 * Filter to modify active tabs logic.
		 *
		 * @since 4.0.0
		 *
		 * @param string $tab  Current tab.
		 * @param array  $tabs Allowed tabs.
		 */
		return apply_filters( '404_to_301_admin_settings_page_get_current_tab', $tab, $tabs );
	}
}
