<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * The main 404 email alert class.
 *
 * This class handles the email alert task for the
 * 404 errors found.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage EmailAlert
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */
class JJ4T3_404_Email {

	/**
	 * Error data class object.
	 *
	 * @var    object
	 * @access private
	 * @since  3.0.0
	 */
	private $error_data;

	/**
	 * Recipient email addresses.
	 *
	 * @var    string|array
	 * @access public
	 */
	public $recipient;

	/**
	 * Email subject.
	 *
	 * @var    string
	 * @access public
	 */
	public $subject;

	/**
	 * Email headers.
	 *
	 * @var    array
	 * @access public
	 */
	public $headers;

	/**
	 * Email content body.
	 *
	 * @var    string
	 * @access public
	 */
	public $body;

	/**
	 * Initialize the class and set properties.
	 *
	 * @param object $error_data Error logs data class.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function __construct( $error_data ) {

		$this->error_data = $error_data;

		// Set required properties.
		$this->set_recipient();
		$this->set_subject();
		$this->set_headers();
		$this->set_body();
	}

	/**
	 * Send email alert about the error.
	 *
	 * Registering new action hook "jj4t3_before_email".
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function send_email() {

		/**
		 * Action hook to perform before email alert.
		 *
		 * Sending email using wp_mail() function.
		 *
		 * @since 3.0.0
		 *
		 * @param string $this ->recipient Email recipient.
		 * @param string $this ->subject   Email subject.
		 * @param string $this ->body      Email body.
		 */
		do_action( 'jj4t3_before_email', $this->recipient, $this->subject, $this->body );

		//var_dump($this->recipient); exit;
		// Send email using wp_mail().
		wp_mail( $this->recipient, $this->subject, $this->body, $this->headers );
	}

	/**
	 * Set email recipients.
	 *
	 * Registering filter - "jj4t3_email_recipient".
	 *
	 * @since  3.0.0
	 * @access private
	 */
	private function set_recipient() {

		// Get email recipient if set.
		$recipient = jj4t3_get_option( 'email_notify_address', get_option( 'admin_email' ) );

		/**
		 * Filter to alter email recipient.
		 *
		 * @since 3.0.0
		 */
		$this->recipient = apply_filters( 'jj4t3_email_recipient', $recipient );
	}

	/**
	 * Set subject for the 404 email alert.
	 *
	 * Registering filter - "jj4t3_email_subject".
	 *
	 * @since  3.0.0
	 * @access private
	 */
	private function set_subject() {

		// Include site title.
		$message = __( 'Snap! One more 404 on ', '404-to-301' ) . get_bloginfo( 'name' );

		/**
		 * Filter to alter email subject text.
		 *
		 * @since 3.0.0
		 */
		$this->subject = apply_filters( 'jj4t3_email_subject', $message );
	}

	/**
	 * Set email headers.
	 *
	 * Registering filter - "jj4t3_email_headers".
	 *
	 * @since  3.0.0
	 * @access private
	 */
	private function set_headers() {

		/**
		 * Filter to alter From name of email alert.
		 *
		 * @since 3.0.0
		 */
		$from_name = apply_filters( 'jj4t3_email_header_name', get_bloginfo( 'name' ) );

		/**
		 * Filter to alter From email address of email alert.
		 *
		 * @since 3.0.0
		 */
		$from_email = apply_filters( 'jj4t3_email_header_email', get_option( 'admin_email' ) );

		$this->headers[] = "From: " . $from_name . " <" . $from_email . ">" . "\r\n";
		$this->headers[] = "Content-Type: text/html; charset=UTF-8";
	}

	/**
	 * Set content for the email alert.
	 *
	 * Registering filter - "jj4t3_email_body".
	 *
	 * @since  3.0.0
	 * @access private
	 */
	private function set_body() {

		$message = "<p>" . __( 'Bummer! You have one more 404', '404-to-301' ) . "</p>";
		$message .= '<table>';
		// 404 path.
		$message .= '<tr>';
		$message .= '<th align="left">' . __( '404 Path', '404-to-301' ) . '</th>';
		$message .= '<td align="left">' . $this->error_data->url . '</td>';
		$message .= '</tr>';
		// IP Address.
		$message .= '<tr>';
		$message .= '<th align="left">' . __( 'IP Address', '404-to-301' ) . '</th>';
		$message .= '<td align="left">' . $this->error_data->ip . '</td>';
		$message .= '</tr>';
		// Date and time.
		$message .= '<tr>';
		$message .= '<th align="left">' . __( 'Time', '404-to-301' ) . '</th>';
		$message .= '<td align="left">' . $this->error_data->time . '</td>';
		$message .= '</tr>';
		// Referral url.
		$message .= '<tr>';
		$message .= '<th align="left">' . __( 'Referral Page', '404-to-301' ) . '</th>';
		$message .= '<td align="left">' . $this->error_data->ref . '</td>';
		$message .= '</tr>';
		$message .= '</table>';
		// Who sent me this alert?
		$message .= '<p>' . sprintf( __( 'Alert sent by the %s404 to 301%s plugin for WordPress.', '404-to-301' ), '<strong>', '</strong>' ) . '</p>';

		/**
		 * Filter to alter email content.
		 *
		 * @since 3.0.0
		 */
		$this->body = apply_filters( 'jj4t3_email_body', $message );
	}

}
