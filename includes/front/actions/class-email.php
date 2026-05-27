<?php
/**
 * Email notification on 404.
 *
 * Sends an email to the configured recipient when a 404 is detected.
 * Honours an `email_threshold` so the inbox doesn't get hammered by
 * busy sites — the email only fires once `hits >= threshold` (i.e.
 * the threshold-th hit triggers the notification).
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Front\Actions;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Front\Request;

/**
 * Class Email
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front\Actions
 */
class Email extends Action {

	/**
	 * Whether this action should fire for the current request.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return bool
	 */
	protected function should_run( Request $request ): bool {
		if ( ! $this->setting( 'email_enabled', false ) ) {
			return false;
		}

		if ( $request->is_excluded() ) {
			return false;
		}

		$recipient = (string) $this->setting( 'email_recipient', '' );
		if ( '' === $recipient || ! is_email( $recipient ) ) {
			return false;
		}

		// Threshold: only email once we have at least N hits. The Log
		// action runs before us, so by the time we get here the
		// `hits` column has already been bumped for this request.
		$threshold = max( 1, (int) $this->setting( 'email_threshold', 1 ) );
		$log       = $request->log();
		$hits      = $log ? (int) $log->hits : 1;

		if ( $hits < $threshold ) {
			return false;
		}

		// Avoid spamming once the threshold has been crossed — only
		// send for the exact hit that hits the threshold (e.g. 5th
		// hit when threshold = 5). Otherwise every subsequent hit
		// would re-trigger.
		if ( $hits > $threshold && 1 !== $threshold ) {
			return false;
		}

		return true;
	}

	/**
	 * Compose and send the notification.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return void
	 */
	public function run( Request $request ): void {
		if ( ! $this->should_run( $request ) ) {
			return;
		}

		$recipient = (string) $this->setting( 'email_recipient', get_option( 'admin_email' ) );

		/* translators: %s: site name */
		$subject = sprintf( __( '[%s] 404 error detected', '404-to-301' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );

		$lines = array(
			__( 'A new 404 error has been detected on your site:', '404-to-301' ),
			'',
			sprintf( '%s: %s', __( 'URL', '404-to-301' ),       $request->url() ),
			sprintf( '%s: %s', __( 'Referer', '404-to-301' ),   $request->referer() ?: __( '(none)', '404-to-301' ) ),
			sprintf( '%s: %s', __( 'IP', '404-to-301' ),        $request->ip() ?: __( '(masked)', '404-to-301' ) ),
			sprintf( '%s: %s', __( 'User-Agent', '404-to-301' ), $request->user_agent() ),
			sprintf( '%s: %s', __( 'Method', '404-to-301' ),    $request->method() ),
			'',
			__( 'You can manage 404 errors and custom redirects in the WordPress admin under "404 to 301".', '404-to-301' ),
		);

		$body = implode( "\n", $lines );

		/**
		 * Filter the notification email recipient, subject and body.
		 *
		 * @since 4.0.0
		 *
		 * @param array{recipient:string,subject:string,body:string} $email   Email payload.
		 * @param Request                                            $request Current request.
		 */
		$email = (array) apply_filters(
			'404_to_301_email_payload',
			array(
				'recipient' => $recipient,
				'subject'   => $subject,
				'body'      => $body,
			),
			$request
		);

		wp_mail( $email['recipient'], $email['subject'], $email['body'] );

		/**
		 * Fires after the notification email has been sent.
		 *
		 * @since 4.0.0
		 *
		 * @param array   $email   Sent payload.
		 * @param Request $request Current request.
		 */
		do_action( '404_to_301_email_sent', $email, $request );
	}
}
