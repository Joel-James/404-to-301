<?php
/**
 * The error redirection class.
 *
 * This class will handle the redirection for 404s.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Actions
 * @subpackage Redirect
 */

namespace DuckDev\Redirect\Actions;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Data;
use DuckDev\Redirect\Models\Request;

/**
 * Class Redirect
 *
 * @extends Action
 * @since   4.0.0
 * @package DuckDev\Redirect
 */
class Redirect extends Action {

	/**
	 * Action type - email.
	 *
	 * @var string $action
	 * @access protected
	 * @since  4.0
	 */
	protected $action = 'redirect';

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0.0
	 *
	 * @return void
	 */
	public function process() {
		/**
		 * Action hook to execute before performing redirect.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		do_action( 'dd4t3_redirect_pre_redirect', $this->request );

		// Perform redirect using WordPress.
		// phpcs:ignore
		wp_redirect(
			$this->target_link(),
			$this->redirect_type()
		);

		// Exit, because WordPress will not exit automatically.
		exit;
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
	private function target_link() {
		// Get custom options.
		$link = $this->request->get_config( 'redirect_target' );

		// If custom target is not set.
		if ( empty( $link ) ) {
			/**
			 * Filter hook to add add or remove redirect types.
			 *
			 * Other plugins can use this filter to add new redirect
			 * types to 404 to 301.
			 *
			 * @param array $types Redirect types.
			 *
			 * @since 4.0
			 */
			$link = apply_filters( 'dd4t3_redirect_default_link', home_url() );

			// Get global target.
			$target = dd4t3_settings()->get( 'redirect_target' );

			// If target is a page.
			if ( 'page' === $target ) {
				// Target page ID.
				$page = dd4t3_settings()->get( 'redirect_page' );
				// Only consider if it's published page/post.
				if ( ! empty( $page ) && 'publish' === get_post_status( $page ) ) {
					$link = get_permalink( $page );
				}
			} else {
				// Get link target.
				$link = dd4t3_settings()->get( 'redirect_link', $link );
			}
		}

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
		return apply_filters( 'dd4t3_redirect_target_link', $link, $this->request );
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
	private function redirect_type() {
		// Get custom options.
		$type = $this->request->get_config( 'redirect_type' );

		// If custom target is not set.
		if ( empty( $type ) ) {
			// Get global target.
			$type = dd4t3_settings()->get( 'redirect_type', 301 );
		}

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
		$type = apply_filters( 'dd4t3_redirect_redirect_type', $type, $this->request );

		return in_array( $type, array_keys( Data::redirect_types() ), true ) ? $type : 301;
	}
}
