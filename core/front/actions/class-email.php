<?php
/**
 * The plugin menu controller class.
 *
 * This class handles the admin menu functionality for the plugin.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Menu
 */

namespace DuckDev\Redirect\Front\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Front\Request;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Email extends Action {

	/**
	 * Action type - email.
	 *
	 * @var string $action
	 *
	 * @since 4.0
	 */
	protected $action = 'email';

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function run() {
		/**
		 * Action hook to execute before sending email.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		do_action( 'dd4t3_email_pre_email', $this->request );

		// Send email using wp_mail().
		$success = wp_mail(
			$this->recipient(),
			$this->subject(),
			$this->body(),
			$this->headers()
		);

		/**
		 * Action hook to execute after sending email.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		do_action( 'dd4t3_email_post_email', $this->request, $success );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	private function recipient() {
		// Get global target.
		$recipient = dd4t3_settings()->get(
			'recipient',
			'email',
			get_option( 'admin_email' )
		);

		/**
		 * Filter hook to enable/disable redirect.
		 *
		 * Other plugins can use this filter to enable
		 * or disable redirect.
		 *
		 * @param bool    $can     Can redirect.
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd4t3_email_recipient', $recipient, $this->request );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	private function subject() {
		// Get global target.
		$recipient = dd4t3_settings()->get(
			'recipient',
			'email',
			get_option( 'admin_email' )
		);

		/**
		 * Filter hook to enable/disable redirect.
		 *
		 * Other plugins can use this filter to enable
		 * or disable redirect.
		 *
		 * @param bool    $can     Can redirect.
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd4t3_email_recipient', $recipient, $this->request );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	private function headers() {
		// Get global target.
		$recipient = dd4t3_settings()->get(
			'recipient',
			'email',
			get_option( 'admin_email' )
		);

		/**
		 * Filter hook to enable/disable redirect.
		 *
		 * Other plugins can use this filter to enable
		 * or disable redirect.
		 *
		 * @param bool    $can     Can redirect.
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd4t3_email_recipient', $recipient, $this->request );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	private function body() {
		// Get global target.
		$recipient = dd4t3_settings()->get(
			'recipient',
			'email',
			get_option( 'admin_email' )
		);

		/**
		 * Filter hook to enable/disable redirect.
		 *
		 * Other plugins can use this filter to enable
		 * or disable redirect.
		 *
		 * @param bool    $can     Can redirect.
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd4t3_email_recipient', $recipient, $this->request );
	}
}
