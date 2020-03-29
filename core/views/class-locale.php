<?php

namespace DuckDev\WP404\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The locale of the plugin.
 *
 * Loading page specific views are handled in this class.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Locale extends Base {

	/**
	 * Get the common vars available to all files.
	 *
	 * This data will be available in all scripts.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function common() {
		return [
			'dialogs' => [],
			'notices' => [],
			'header'  => [],
			'footer'  => [],
			'labels'  => [
				'items'        => __( '%d items', '404-to-301' ),
				'current_page' => __( 'Current Page', '404-to-301' ),
				'of'           => __( 'of', '404-to-301' ),
				'page_of'      => __( '%d of', '404-to-301' ),
				'search'       => __( 'Search', '404-to-301' ),
			],
			'buttons' => [
				'save_changes' => __( 'Save Changes', 'wpmudev_vids' ),
				'apply'        => __( 'Apply', '404-to-301' ),
			],
		];
	}

	/**
	 * Get the loclization vars for the dashboard page.
	 *
	 * This data will be only available in dashboard scripts.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public function logs() {
		return [
			'titles'       => [
				'dashboard' => __( 'Dashboard', '404-to-301' ),
			],
			'labels'       => [
				'path'       => __( 'Path', '404-to-301' ),
				'date'       => __( 'Date', '404-to-301' ),
				'referral'   => __( 'Referral', '404-to-301' ),
				'ip_address' => __( 'IP Address', '404-to-301' ),
				'user_agent' => __( 'User Agent', '404-to-301' ),
				'move_trash' => __( 'Move to Trash', '404-to-301' ),
				'delete_all' => __( 'Delete All', '404-to-301' ),
				'group_by'   => __( 'Group by', '404-to-301' ),
				'path_404'   => __( '404 Path', '404-to-301' ),
			],
			'buttons'      => [
				'install_dash' => __( 'Install Plugin', '404-to-301' ),
			],
			'notices'      => [
				'confirm_delete_all' => __( 'Are you sure you want to delete all these logs?', '404-to-301' ),
			],
			'descriptions' => [
				'no_data'      => __( 'No data found.', '404-to-301' ),
				/* translators: %s: Name of the current user. */
				'install_dash' => __( '%s, welcome to Integrated Video Tutorials - the best tutorials plugin for WordPress. It looks like you don\'t have the WPMU DEV Dashboard.', 'wpmudev_vids' ),
			],
		];
	}

	/**
	 * Get the loclization vars for the settings page.
	 *
	 * This data will be only available in settings scripts.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public function settings() {
		return [
			'titles'       => [
				'settings' => __( 'Settings', '404-to-301' ),
				'general'  => __( 'General', '404-to-301' ),
				'email'    => __( 'Email', '404-to-301' ),
			],
			'labels'       => [
				'redirect_type'      => __( 'Redirect type', '404-to-301' ),
				'redirect_to'        => __( 'Redirect to', '404-to-301' ),
				'select_page'        => __( 'Select the page', '404-to-301' ),
				'custom_url'         => __( 'Custom URL', '404-to-301' ),
				'log_errors'         => __( 'Log 404 Errors', '404-to-301' ),
				'disable_guess'      => __( 'Disable URL guessing', '404-to-301' ),
				'exclude_paths'      => __( 'Exclude paths', '404-to-301' ),
				'redirect_301'       => __( '301 Redirect (SEO)', '404-to-301' ),
				'redirect_302'       => __( '302 Redirect', '404-to-301' ),
				'redirect_307'       => __( '307 Redirect', '404-to-301' ),
				'redirect_404'       => __( '404 Not found', '404-to-301' ),
				'existing_page'      => __( 'Existing Page', '404-to-301' ),
				'no_redirect'        => __( 'No Redirect', '404-to-301' ),
				'email_notification' => __( 'Email notifications', '404-to-301' ),
				'email_address'      => __( 'Email address', '404-to-301' ),
			],
			'notices'      => [
				'settings_updated'       => __( 'Settings updated successfully.', '404-to-301' ),
				'settings_update_failed' => __( 'Oops! Something went wrong.', '404-to-301' ),
			],
			'descriptions' => [
				'select_page'       => __( 'Select any WordPress page as a 404 page.', 'wpmudev_vids' ),
				'custom_url'        => __( 'Redirect 404 requests to a specific URL.', '404-to-301' ),
				'disable_redirect'  => __( 'To disable redirect.', '404-to-301' ),
				'override_settings' => __( 'You can override this by setting individual custom redirects from error logs list.', '404-to-301' ),
			],
		];
	}
}