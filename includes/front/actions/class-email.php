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

use DuckDev\FourNotFour\Plugin;
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

		// Decoded site name — used in both the subject and the body
		// greeting so a forwarded email is self-identifying without
		// the recipient needing to scroll up to the headers.
		$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		/* translators: %s: site name */
		$subject = sprintf( __( '[%s] 404 error detected', '404-to-301' ), $site_name );

		// Re-fetch the log so we can surface the running hit count. The
		// Log action upstream of us already bumped this for the current
		// request, so the number reflects every hit on this URL so far
		// (including this one). Falls back to 1 if the log row isn't
		// available for any reason.
		$log  = $request->log();
		$hits = $log ? (int) $log->hits : 1;

		// Deep link straight to the Logs admin page so the recipient
		// can review and triage from the email. We don't link to the
		// specific row because the logs list is filterable / paginated
		// client-side — landing on the list is the closest stable
		// target available today.
		$logs_url = admin_url( 'admin.php?page=' . Plugin::PAGE_LOGS );

		// Localised "when did this happen" stamp. `wp_date()` formats
		// using the site's configured timezone (Settings → General),
		// not the server's PHP default, so the time matches what the
		// admin sees elsewhere in WP. Format combines the site's
		// `date_format` and `time_format` options for consistency
		// with how core renders timestamps in the dashboard.
		$timestamp = wp_date(
			get_option( 'date_format' ) . ' ' . get_option( 'time_format' )
		);

		$lines = array(
			sprintf(
				/* translators: %s: site name. */
				__( 'A new 404 error has been detected on %s:', '404-to-301' ),
				$site_name
			),
			'',
			sprintf( '%s: %s', __( 'URL', '404-to-301' ), $request->url() ),
			sprintf(
				/* translators: %d: total number of times this URL has 404'd. */
				_n( '%d hit so far', '%d hits so far', $hits, '404-to-301' ),
				$hits
			),
			sprintf( '%s: %s', __( 'Time', '404-to-301' ), $timestamp ),
			sprintf( '%s: %s', __( 'Referer', '404-to-301' ), $request->referer() ? $request->referer() : __( '(none)', '404-to-301' ) ),
			sprintf( '%s: %s', __( 'IP', '404-to-301' ), $request->ip() ? $request->ip() : __( '(masked)', '404-to-301' ) ),
			sprintf( '%s: %s', __( 'User-Agent', '404-to-301' ), $request->user_agent() ),
			sprintf( '%s: %s', __( 'Method', '404-to-301' ), $request->method() ),
			'',
			__( 'Review and manage 404 errors here:', '404-to-301' ),
			$logs_url,
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
