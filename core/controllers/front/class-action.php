<?php

namespace DuckDev\WP404\Controllers\Front;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use Exception;
use DuckDev\WP404\Helpers\Settings;
use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The redirect functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Action extends Base {

	/**
	 * Initialize the redirect functionality.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function process() {
		// Admin side is exceptional!
		if ( is_admin() ) {
			return;
		}

		// We are not breaking bad.
		try {
			$this->log();

			$this->notification();

			$this->redirect();

		} catch ( Exception $e ) {
			// Who cares?
		}
	}

	/**
	 * This method will handle the redirect functionality.
	 *
	 * If the redirect is set to `404` status, we need to handle
	 * that in different way. See `Redirect_404` for that.
	 *
	 * @since 4.0
	 */
	private function redirect() {
		/**
		 * Filter to disable redirect for error.
		 *
		 * @param bool $can Can we redirect.
		 *
		 * @since 3.0.0
		 */
		if ( apply_filters( '404_to_301_can_redirect', true ) ) {
			// Send email notification.
			Redirect::get()->process();
		}
	}

	/**
	 * This method will handle the email notification functionality.
	 *
	 * Email notification is sent for each 404 error. Plugins can override
	 * this using `404_to_301_can_email_alert` filter.
	 *
	 * @since 4.0
	 */
	private function notification() {
		/**
		 * Filter to disable email alerts.
		 *
		 * @param bool $can Can we send email.
		 *
		 * @since 3.0.0
		 */
		if ( apply_filters( '404_to_301_can_email_alert', true ) ) {
			// Send email notification.
			Email::get()->process();
		}
	}

	/**
	 * This method will handle the error logging functionality
	 *
	 * Error logs are stored in custom table called `404_to_301`.
	 *
	 * @since 4.0
	 */
	private function log() {
		/**
		 * Filter to disable error log for 404s.
		 *
		 * @param bool $can Can we log error.
		 *
		 * @since 3.0.0
		 */
		if ( apply_filters( '404_to_301_can_email_alert', true ) ) {
			// Send email notification.
			Log::get()->process();
		}
	}
}
