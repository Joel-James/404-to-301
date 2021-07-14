<?php
/**
 * Asynchronous request class.
 *
 * This class helps to execute no blocking tasks with
 * WordPress. This is modified version from
 * https://github.com/deliciousbrains/wp-background-processing
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Queue
 * @subpackage Async
 */

namespace DuckDev\Redirect\Utils\Libs;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Async.
 *
 * @package DuckDev\Redirect
 * @abstract
 */
abstract class Async {

	/**
	 * Prefix
	 *
	 * (default value: 'wp')
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = 'dd404';

	/**
	 * Action
	 *
	 * (default value: 'async_request')
	 *
	 * @var string
	 * @access protected
	 */
	protected $action = 'async_request';

	/**
	 * Identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $identifier;

	/**
	 * Data array.
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $data = array();

	/**
	 * Initiate new async request
	 */
	public function __construct() {
		// Set identifier.
		$this->identifier = $this->prefix . '_' . $this->action;

		// Register actions.
		add_action( 'wp_ajax_' . $this->identifier, array( $this, 'maybe_handle' ) );
		add_action( 'wp_ajax_nopriv_' . $this->identifier, array( $this, 'maybe_handle' ) );
	}

	/**
	 * Set data used during the request
	 *
	 * @param array $data Data.
	 *
	 * @return $this
	 */
	public function data( $data ) {
		$this->data = $data;

		return $this;
	}

	/**
	 * Dispatch the async request
	 *
	 * @return array|WP_Error
	 */
	public function dispatch() {
		$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
		$args = $this->get_post_args();

		return wp_remote_post( esc_url_raw( $url ), $args );
	}

	/**
	 * Get query args
	 *
	 * @return array
	 */
	protected function get_query_args() {
		if ( property_exists( $this, 'query_args' ) ) {
			return $this->query_args;
		}

		$args = array(
			'action' => $this->identifier,
			'nonce'  => wp_create_nonce( $this->identifier ),
		);

		/**
		 * Filters the post arguments used during an async request.
		 *
		 * @param array $url
		 */
		return apply_filters( $this->identifier . '_query_args', $args );
	}

	/**
	 * Get query URL
	 *
	 * @return string
	 */
	protected function get_query_url() {
		if ( property_exists( $this, 'query_url' ) ) {
			return $this->query_url;
		}

		$url = admin_url( 'admin-ajax.php' );

		/**
		 * Filters the post arguments used during an async request.
		 *
		 * @param string $url
		 */
		return apply_filters( $this->identifier . '_query_url', $url );
	}

	/**
	 * Get post args
	 *
	 * @return array
	 */
	protected function get_post_args() {
		if ( property_exists( $this, 'post_args' ) ) {
			return $this->post_args;
		}

		$args = array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'body'      => $this->data,
			'cookies'   => $_COOKIE,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		);

		/**
		 * Filters the post arguments used during an async request.
		 *
		 * @param array $args
		 */
		return apply_filters( $this->identifier . '_post_args', $args );
	}

	/**
	 * Maybe handle
	 *
	 * Check for correct nonce and pass to handler.
	 */
	public function maybe_handle() {
		// Don't lock up other requests while processing.
		session_write_close();

		check_ajax_referer( $this->identifier, 'nonce' );

		$this->handle();

		wp_die();
	}

	/**
	 * Handle
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 */
	abstract protected function handle();

}
