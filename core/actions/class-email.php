<?php
/**
 * The error email class.
 *
 * This class will send email notifications for the 404 error
 * in details to the recipients.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Actions
 * @subpackage Email
 */

namespace DuckDev\Redirect\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Models\Request;

/**
 * Class Email
 *
 * @since   4.0.0
 * @extends Action
 * @package DuckDev\Redirect
 */
class Email extends Action {

	/**
	 * Action type - email.
	 *
	 * @var string $action
	 * @access protected
	 * @since  4.0.0
	 */
	protected $action = 'email';

	/**
	 * Process the email notification action.
	 *
	 * Send email using wp_mail function.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return void
	 */
	public function process() {
		/**
		 * Action hook to execute before sending email.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_email_pre_email', $this->request );

		// Send email using wp_mail().
		$success = wp_mail(
			$this->get_recipient(),
			$this->get_subject(),
			$this->get_body(),
			$this->get_headers()
		);

		/**
		 * Action hook to execute after sending email.
		 *
		 * @param Request $request Request object.
		 * @param bool    $success Is email sent.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_email_post_email', $this->request, $success );
	}

	/**
	 * Get email recipient address.
	 *
	 * Use `dd4t3_email_get_recipient` filter to modify the email recipient.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return string
	 */
	private function get_recipient() {
		// Get the email address.
		$recipient = dd4t3_settings()->get(
			'email_recipient',
			get_option( 'admin_email' ) // Site admin email as default.
		);

		/**
		 * Filter hook to modify email recipient.
		 *
		 * @param bool    $recipient Recipient.
		 * @param Request $request   Request object.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_email_get_recipient', $recipient, $this->request );
	}

	/**
	 * Get the email subject.
	 *
	 * Use `dd4t3_email_get_subject` filter to modify the email subject.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return string
	 */
	private function get_subject() {
		// Get the email subject.
		$subject = __( 'New 404 Error', '404-to=301' );

		/**
		 * Filter hook to modify email subject.
		 *
		 * @param bool    $subject Subject.
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_email_get_subject', $subject, $this->request );
	}

	/**
	 * Get the email headers.
	 *
	 * Use `dd4t3_email_get_headers` filter to modify the email headers.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return array
	 */
	private function get_headers() {
		/**
		 * Filter to alter "From" name of email alert.
		 *
		 * @param string $name From name (Default: Site name).
		 *
		 * @since 3.0.0
		 */
		$name = apply_filters( 'dd4t3_email_get_headers_name', get_bloginfo( 'name' ) );

		/**
		 * Filter to alter From email address of email alert.
		 *
		 * @param string $from From email (Default: admin_email option).
		 *
		 * @since 3.0.0
		 */
		$from = apply_filters( 'dd4t3_email_get_headers_from', get_option( 'admin_email' ) );

		// Get the email headers.
		$headers = array(
			"From: $name <$from>\r\n",
			'Content-Type: text/html; charset=UTF-8',
		);

		/**
		 * Filter hook to modify email headers.
		 *
		 * @param bool    $headers Headers.
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_email_get_headers', $headers, $this->request );
	}

	/**
	 * Get the email body.
	 *
	 * Use `dd4t3_email_get_body` filter to modify the email body.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return string
	 */
	private function get_body() {
		// Get the email body.
		$body = '';

		/**
		 * Filter hook to modify email body.
		 *
		 * @param bool    $subject Body.
		 * @param Request $request Request object.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( 'dd4t3_email_get_body', $body, $this->request );
	}
}
