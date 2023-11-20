<?php
/**
 * The error email class.
 *
 * This class will send email notifications for the 404 error.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Actions
 * @subpackage Email
 */

namespace RedirectPress\Front\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use RedirectPress\Front\Request;

/**
 * Class Email
 *
 * @since   4.0.0
 * @extends Action
 * @package RedirectPress\Front\Actions
 */
class Email extends Action {

	/**
	 * Action type - email.
	 *
	 * @since  4.0.0
	 * @var string $action
	 * @access protected
	 */
	protected $action = 'email';

	/**
	 * Perform 404 email action if required.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function process_error() {
		// Abort action.
		if ( ! $this->can_proceed() ) {
			return;
		}

		// Action not enabled.
		if ( ! $this->is_enabled( 'email_status', 'email_enabled' ) ) {
			return;
		}

		// Send email.
		$this->notify();
	}

	/**
	 * Send an email to notify about 404 error.
	 *
	 * @since  4.0.0
	 * @access public
	 * @return void
	 */
	private function notify() {
		// Get required data.
		$recipient = $this->get_recipient();
		$subject   = $this->get_subject();
		$body      = $this->get_body();
		$headers   = $this->get_headers();

		/**
		 * Action hook to execute before performing a redirect.
		 *
		 * @since 4.0
		 *
		 * @param string|string[] $recipient Email recipient.
		 * @param string          $subject   Email subject.
		 * @param string          $body      Email content.
		 * @param array           $headers   Email headers.
		 * @param Request         $request   Request object.
		 */
		do_action( 'redirectpress_before_email', $recipient, $subject, $body, $headers, $this->request );

		// Send email using wp_mail().
		$success = wp_mail( $recipient, $subject, $body, $headers );

		/**
		 * Action hook to execute after adding a log.
		 *
		 * This will be fired even if the log creation failed.
		 * Please check the $success param to know if the log is created.
		 *
		 * @since 4.0
		 *
		 * @param bool|mixed|void $success   Email status.
		 * @param string          $recipient Email recipient.
		 * @param string          $subject   Email subject.
		 * @param string          $body      Email content.
		 * @param array           $headers   Email headers.
		 * @param Request         $request   Request object.
		 */
		do_action( 'redirectpress_after_email', $success, $recipient, $subject, $body, $headers, $this->request );
	}

	/**
	 * Get email recipient address.
	 *
	 * Use `redirectpress_email_get_recipient` filter to modify the email recipient.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return string
	 */
	private function get_recipient() {
		// Get the email address.
		$recipient = redirectpress_settings()->get(
			'email_recipient',
			get_option( 'admin_email' ) // Site admin email as default.
		);

		/**
		 * Filter hook to modify email recipient.
		 *
		 * @since 4.0.0
		 *
		 * @param string|string[] $recipient Email recipient.
		 * @param Request         $request   Request object.
		 */
		return apply_filters( 'redirectpress_email_get_recipient', $recipient, $this->request );
	}

	/**
	 * Get the email subject.
	 *
	 * Use `redirectpress_email_get_subject` filter to modify the email subject.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return string
	 */
	private function get_subject() {
		// Get the email subject.
		$subject = __( 'New 404 Error', '404-to-301' );

		/**
		 * Filter hook to modify email subject.
		 *
		 * @since 4.0.0
		 *
		 * @param string  $subject Subject.
		 * @param Request $request Request object.
		 */
		return apply_filters( 'redirectpress_email_get_subject', $subject, $this->request );
	}

	/**
	 * Get the email headers.
	 *
	 * Use `redirectpress_email_get_headers` filter to modify the email headers.
	 *
	 * @since  4.0.0
	 * @access private
	 * @return array
	 */
	private function get_headers() {
		/**
		 * Filter to alter "From" name of email alert.
		 *
		 * @since 3.0.0
		 *
		 * @param string $name From name (Default: Site name).
		 */
		$name = apply_filters( 'redirectpress_email_get_headers_name', get_bloginfo( 'name' ) );

		/**
		 * Filter to alter From email address of email alert.
		 *
		 * @since 3.0.0
		 *
		 * @param string $from From email (Default: admin_email option).
		 */
		$from = apply_filters( 'redirectpress_email_get_headers_from', get_option( 'admin_email' ) );

		// Get the email headers.
		$headers = array(
			"From: $name <$from>\r\n",
			'Content-Type: text/html; charset=UTF-8',
		);

		/**
		 * Filter hook to modify email headers.
		 *
		 * @since 4.0.0
		 *
		 * @param array   $headers Email headers.
		 * @param Request $request Request object.
		 */
		return apply_filters( 'redirectpress_email_get_headers', $headers, $this->request );
	}

	/**
	 * Get the email body.
	 *
	 * Use `redirectpress_email_get_body` filter to modify the email body.
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
		 * @since 4.0.0
		 *
		 * @param string  $subject Email body.
		 * @param Request $request Request object.
		 */
		return apply_filters( 'redirectpress_email_get_body', $body, $this->request );
	}
}
