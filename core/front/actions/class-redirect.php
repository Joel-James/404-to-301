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
use DuckDev\Redirect\Models\Query;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Redirect extends Action {

	/**
	 * Action type - email.
	 *
	 * @var string $action
	 *
	 * @since 4.0
	 */
	protected $action = 'redirect';

	/**
	 * Get available redirect types.
	 *
	 * Use `dd404_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function run() {
		/**
		 * Action hook to execute before performing redirect.
		 *
		 * @param Request $request Request object.
		 *
		 * @since 4.0
		 */
		do_action( 'dd404_redirect_pre_redirect', $this->request );
		$query = new Query();
		$query->table( 'wp_404_to_301' )
		      ->select( array( 'id', 'url', 'ip', 'comment_author_url' ) )
		      ->join( 'wp_comments', 'wp_404_to_301.id', 'wp_comments.comment_ID' )
		      ->where( 'url', 'tests' )
		      ->where( 'ip', 'dd', '!=' )
		      ->or_where( 'ua', 'nones' )
		      ->or_where(
			      array(
				      array(
					      'field' => 'ua',
					      'value' => 'none',
				      ),
				      array(
					      'field' => 'url',
					      'value' => 'test',
					      //'operator' => '>',
				      ),
			      )
		      )
		      ->order_by( 'id' );
		//$query->group_by( 'id' );
		error_log( print_r( $query->get_row(), true ) );

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
	 * Use `dd404_redirect_types` filter to add
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
			$link = apply_filters( 'dd404_redirect_default_link', home_url() );

			// Get global target.
			$target = dd404_settings()->get( 'target', 'redirect' );

			// If target is a page.
			if ( 'page' === $target ) {
				// Target page ID.
				$page = dd404_settings()->get( 'page', 'redirect' );
				// Only consider if it's published page/post.
				if ( ! empty( $page ) && 'publish' === get_post_status( $page ) ) {
					$link = get_permalink( $page );
				}
			} else {
				// Get link target.
				$link = dd404_settings()->get( 'link', 'redirect', $link );
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
		return apply_filters( 'dd404_redirect_target_link', $link, $this->request );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd404_redirect_types` filter to add
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
			$type = dd404_settings()->get(
				'type',
				'redirect',
				301
			);
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
		$type = apply_filters( 'dd404_redirect_redirect_type', $type, $this->request );

		return in_array( $type, array_keys( self::types() ), true ) ? $type : 301;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd404_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public static function types() {
		// Sub page.
		$types = array(
			301 => __( '301', '404-to-301' ),
			302 => __( '302', '404-to-301' ),
			307 => __( '307', '404-to-301' ),
			404 => __( '404', '404-to-301' ),
		);

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
		return apply_filters( 'dd404_redirect_types', $types );
	}
}
