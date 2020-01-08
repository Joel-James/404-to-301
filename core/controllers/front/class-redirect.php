<?php

namespace DuckDev\WP404\Controllers\Front;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Controllers\Front\Redirect\Redirect_404;
use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * The redirect functionality of the plugin.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class Redirect extends Base {

	protected $url;

	/**
	 * Initialize the redirect functionality.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	public function process( $type = '301' ) {
		switch ( $type ) {
			case '404':
				$this->handle_404();
				break;
			default:
				$this->redirect( $type );
				break;
		}
	}

	public function set_url( $url ) {
		$this->url = $url;
	}

	public function set_status( $type ) {
		$this->type = $type;
	}

	/**
	 * Redirect the page to custom page.
	 *
	 * @param int $type Redirect type.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function redirect( $type ) {
		// Redirect to custom page.
		if ( ! empty( $this->url ) ) {
			wp_redirect( $this->url, $type );
			// We need to exit because WP is lazy.
			exit;
		}
	}


	/**
	 * Handle 404 redirect request.
	 *
	 * @since 4.0
	 */
	public function handle_404() {
		Redirect_404::_get()->init();
	}
}
