<?php
/**
 * The redirection class.
 *
 * This class will handle the redirection for all URLs.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Actions
 * @subpackage Redirect
 */

namespace RedirectPress\Front\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use RedirectPress\Data;
use RedirectPress\Front\Request;

/**
 * Class Redirect
 *
 * @extends Action
 * @since   4.0.0
 * @package RedirectPress\Front\Actions
 */
class Redirect extends Action {

	/**
	 * Action type - redirect.
	 *
	 * @since  4.0
	 * @var string $action
	 * @access protected
	 */
	protected $action = 'redirect';

	/**
	 * Action priority for hook.
	 *
	 * Redirect should be performed after all other actions.
	 *
	 * @since  4.0.0
	 * @access protected
	 * @var int $priority
	 */
	protected $priority = 999;

	/**
	 * Process the redirect action if required.
	 *
	 * Redirect if the current URL matches any custom redirects.
	 * All 404 redirects will be executed before this hook. So if the
	 * current request is 404, we won't reach here unless the redirect
	 * is disabled.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function process_request() {
		// Abort action.
		if ( ! $this->can_proceed() ) {
			return;
		}

		// Get custom redirect for current request.
		$destination = $this->request->get_redirect( 'destination' );
		$code        = $this->request->get_redirect( 'code', 301 );
		$status      = $this->request->get_redirect( 'status', 'enabled' );

		// Do custom redirect if required.
		if ( 'enabled' === $status && $code && $destination ) {
			$this->redirect( $destination, $status );
		}
	}

	/**
	 * Perform 404 redirect action if required.
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

		// If a custom redirect is found, it will be taken care separately.
		if ( $this->request->get_redirect( 'destination' ) ) {
			return;
		}

		// Action not enabled.
		if ( ! $this->is_enabled( 'redirect_status', 'redirect_enabled' ) ) {
			return;
		}

		// Get 404 redirect data.
		$code        = $this->get_code();
		$destination = $this->get_destination();

		// Now redirect.
		if ( $code && $destination ) {
			$this->redirect( $destination, $code );
		}
	}

	/**
	 * Get 404 redirect destination URL.
	 *
	 * This is not used if a custom redirect is setup.
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	private function get_destination() {
		$destination = home_url();

		// If target is a page.
		if ( 'page' === redirectpress_settings()->get( 'redirect_target' ) ) {
			// Target page ID.
			$page = redirectpress_settings()->get( 'redirect_page' );
			// Only consider if it's published page/post.
			if ( ! empty( $page ) && 'publish' === get_post_status( $page ) ) {
				$destination = get_permalink( $page );
			}
		} else {
			// Get link target.
			$destination = redirectpress_settings()->get( 'redirect_link', $destination );
		}

		/**
		 * Filter hook to modify 404 redirect destination.
		 *
		 * @since 4.0
		 *
		 * @param string  $destination Destination URL.
		 * @param Request $request     Request object.
		 */
		return apply_filters( 'redirectpress_redirect_get_destination', $destination, $this->request );
	}

	/**
	 * Get 404 redirect status code.
	 *
	 * Only allowed redirect types will be used.
	 *
	 * @since  4.0
	 *
	 * @return bool
	 */
	private function get_code() {
		// Get redirect status code.
		$code = redirectpress_settings()->get( 'redirect_type', 301 );

		/**
		 * Filter hook to modify 404 redirect status code.
		 *
		 * @since 4.0
		 *
		 * @param int     $code    Redirect status code.
		 * @param Request $request Request object.
		 */
		$code = apply_filters( 'redirectpress_redirect_get_code', $code, $this->request );

		return in_array( $code, array_keys( Data::redirect_types() ), true ) ? $code : 301;
	}

	/**
	 * Perform a redirect to a URL.
	 *
	 * All custom redirects and 404 redirects should be processed here.
	 *
	 * @since 4.0
	 *
	 * @param string $target Target URL.
	 * @param int    $status Redirect status.
	 *
	 * @return void
	 */
	private function redirect( $target, $status = 301 ) {
		/**
		 * Filter hook to modify redirect target right before performing redirect.
		 *
		 * @since 4.0
		 *
		 * @param string $target Redirect target.
		 */
		$target = apply_filters( 'redirectpress_redirect_target', $target );

		/**
		 * Filter hook to modify redirect status right before performing redirect.
		 *
		 * @since 4.0
		 *
		 * @param int $status Redirect status code.
		 */
		$status = apply_filters( 'redirectpress_redirect_status_code', $status );

		/**
		 * Action hook to execute before performing a redirect.
		 *
		 * @since 4.0
		 *
		 * @param string  $target  Redirect target.
		 * @param int     $status  Redirect status code.
		 * @param Request $request Request object.
		 */
		do_action( 'redirectpress_before_redirect', $target, $status, $this->request );

		// Perform redirect using WordPress.
		wp_redirect(
			wp_sanitize_redirect( $target ),
			$status
		);

		/**
		 * Action hook to execute after performing a redirect.
		 *
		 * @since 4.0
		 *
		 * @param string  $target  Redirect target.
		 * @param int     $status  Redirect status code.
		 * @param Request $request Request object.
		 */
		do_action( 'redirectpress_after_redirect', $target, $status, $this->request );

		// Exit, because WordPress will not exit automatically.
		exit;
	}
}
