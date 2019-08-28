<?php

namespace DuckDev\WP404\Controllers\Front\Redirect;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use WP_Query;
use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The 404 template functionality.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Redirect_404 extends Base {

	/**
	 * Page ID to set as 404 page.
	 *
	 * @var int $page
	 * @since 4.0
	 */
	protected $page = 2;

	/**
	 * Initialize the redirect functionality.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function init() {
		// Set custom page as 404 template.
		add_filter( '404_template', [ $this, 'template' ], 999 );
	}

	/**
	 * Get our custom 404 page template for error.
	 *
	 * @param string $template Full path to page template file.
	 *
	 * @since 4.0
	 *
	 * @return string $template Full path to page template file.
	 */
	public function template( $template ) {
		global $wp_query;

		/**
		 * bbPress compatibility.
		 *
		 * If a bbPress member page is shown and the member has no topics created yet
		 * the 404_template filter hook fires.
		 *
		 * @see https://wordpress.org/support/topic/not-fully-bbpress-compatible/
		 * @see https://bbpress.trac.wordpress.org/ticket/3161
		 */
		if ( function_exists( 'bbp_is_single_user' ) ) {
			if ( bbp_is_single_user() ) {
				return $template;
			}
		}

		// Setup new query for the template using page ID.
		$wp_query = new WP_Query();
		$wp_query->query( 'page_id=' . $this->page );
		$wp_query->the_post();

		// Get new template.
		$template = get_page_template();

		// Rewind the loop posts.
		rewind_posts();

		return $template;
	}
}
