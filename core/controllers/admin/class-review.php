<?php

namespace DuckDev\WP404\Controllers\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;
use DuckDev\WP404\Helpers;

/**
 * The review notice functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Review extends Base {

	/**
	 * Initilize the class by registering the hooks.
	 *
	 * @since 4.0.0
	 */
	public function init() {
		add_action( 'admin_notices', [ $this, 'notice' ] );
		add_action( 'admin_init', [ $this, 'action' ] );
	}

	/**
	 * Show admin to ask for review in wp.org.
	 *
	 * Show admin notice only inside our plugin's settings page.
	 * Hide the notice permanently if user dismissed it.
	 *
	 * @since 3.0.4
	 *
	 * @return void|bool
	 */
	public function notice() {
		// Only on our page.
		if ( Helpers\General::is_our_page() ) {
			// Only for admins.
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}
			// Get the notice time.
			$notice_time = get_option( '404_to_301_review_notice' );
			// If not set, set now and bail.
			if ( ! $notice_time ) {
				// Set to next week.
				return update_option( '404_to_301_review_notice', time() + 604800 );
			}

			// Current logged in user.
			$current_user = wp_get_current_user();

			// Did the current user already dismiss?.
			$dismissed = get_user_meta( $current_user->ID, '404_to_301_review_notice_dismissed', true );

			// Continue only when allowed.
			if ( (int) $notice_time <= time() && ! $dismissed ) {
				Helpers\General::view( 'admin/notices/review', [
					'current_user' => $current_user,
					'dismissed'    => $dismissed,
				] );
			}
		}
	}

	/**
	 * Handle review notice link actions.
	 *
	 * If dismissed set a user meta for the current user and do not show again.
	 * If agreed to review later, update the review timestamp to after 2 weeks.
	 * NOTE: We are using old prefix for the meta key. Do not change that unless
	 * you can migrating old meta properly. If change it, the review notices will
	 * be shown again.
	 *
	 * @since 3.0.4
	 *
	 * @return void
	 */
	public function action() {
		// Get the current review action.
		$action = Helpers\Request::get( 'review_action' );

		switch ( $action ) {
			case 'later':
				// Let's show after another 2 weeks.
				update_option( '404_to_301_review_notice', time() + 1209600 );
				break;
			case 'dismiss':
				// Do not show again to this user.
				update_user_meta( get_current_user_id(), '404_to_301_review_notice_dismissed', 1 );
				break;
		}
	}
}
