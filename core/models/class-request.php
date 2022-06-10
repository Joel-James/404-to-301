<?php
/**
 * The request object for all actions.
 *
 * This class sets/gets all data required for the actions
 * from the current request.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    Model
 * @subpackage Request
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Request
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Models
 */
class Request {

	/**
	 * Current request URL.
	 *
	 * @since 4.0
	 * @var string $url
	 */
	private $url = '';

	/**
	 * Current request method.
	 *
	 * @since 4.0
	 * @var string $method
	 */
	private $method = 'GET';

	/**
	 * Current request referer.
	 *
	 * @since 4.0
	 * @var string $referrer
	 */
	private $referrer = '';

	/**
	 * Current request IP address.
	 *
	 * @since 4.0
	 * @var string $ip
	 */
	private $ip = '';

	/**
	 * Current request user agent.
	 *
	 * @since 4.0
	 * @var string $agent
	 */
	private $agent = '';

	/**
	 * Get current request headers.
	 *
	 * @since 4.0
	 * @var array $headers
	 */
	private $headers = array();

	/**
	 * Get current request extra data.
	 *
	 * @since 4.0
	 * @var array $request
	 */
	private $others = array();

	/**
	 * Get current request's matching error log.
	 *
	 * @since 4.0
	 * @var object|false $log
	 */
	private $log = false;

	/**
	 * Get current request's custom redirect data.
	 *
	 * @since 4.0
	 * @var object|false $redirect
	 */
	private $redirect = false;

	/**
	 * Create new request object.
	 *
	 * Please use DuckDev\Redirect\Front::get_request() method to
	 * get the current request object. Creating new instances every time
	 * is a bad idea. Do only if required.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->set_ip();
		$this->set_url();
		$this->set_agent();
		$this->set_method();
		$this->set_referer();
		$this->set_headers();
		$this->set_others();
		$this->set_redirect();
		$this->set_log();
	}

	/**
	 * Get current request method.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Get current request referer.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_referer() {
		return $this->referrer;
	}

	/**
	 * Get current request user agent name.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_agent() {
		return $this->agent;
	}

	/**
	 * Get current request IP address.
	 *
	 * Masking IP address will be done while using it.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_ip() {
		return $this->ip;
	}

	/**
	 * Get current request URL path.
	 *
	 * This is the main property we need for all actions.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Get current request header items.
	 *
	 * Few of the sensitive header items are ignored.
	 * See set_headers method for more.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get_headers() {
		return $this->headers;
	}

	/**
	 * Get if any extra data available for current request.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get_others() {
		return $this->others;
	}

	/**
	 * Get custom redirect data for the request.
	 *
	 * If no custom redirect is set, it will return false.
	 * If a name is given, it will return the single item
	 * or else the entire data.
	 *
	 * @since 4.0
	 *
	 * @param bool|string $name    Name of the item.
	 * @param bool        $default Default value.
	 *
	 * @return object|mixed
	 */
	public function get_redirect( $name = false, $default = false ) {
		return $this->get_item( $this->redirect, $name, $default );
	}

	/**
	 * Get log data for the request.
	 *
	 * If no matching log is set, it will return false.
	 * If a name is given, it will return the single item
	 * or else the entire data.
	 *
	 * @since 4.0
	 *
	 * @param bool|string $name    Name of the item.
	 * @param bool        $default Default value.
	 *
	 * @return object|mixed
	 */
	public function get_log( $name = false, $default = false ) {
		return $this->get_item( $this->log, $name, $default );
	}

	/**
	 * Set current request method.
	 *
	 * We will use REQUEST_METHOD value from the server.
	 * Default method wille be "GET".
	 * This should be called by constructor method only.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function set_method() {
		// phpcs:ignore
		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';

		/**
		 * Filter hook to modify current request method.
		 *
		 * @since 4.0
		 *
		 * @param string  $method Request method.
		 * @param Request $this   Request instance.
		 */
		$this->method = apply_filters( 'dd4t3_request_method', $method, $this );
	}

	/**
	 * Set current request referer.
	 *
	 * We will use HTTP_REFERER value from the server.
	 * This should be called by constructor method only.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function set_referer() {
		// phpcs:ignore
		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

		/**
		 * Filter hook to modify current request referer.
		 *
		 * @since 4.0
		 *
		 * @param string  $referrer Request referer.
		 * @param Request $this     Request instance.
		 */
		$this->referrer = apply_filters( 'dd4t3_request_referer', $referrer, $this );
	}

	/**
	 * Set current request user agent.
	 *
	 * We will use HTTP_USER_AGENT value from the server.
	 * This should be called by constructor method only.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function set_agent() {
		// phpcs:ignore
		$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';

		/**
		 * Filter hook to modify current request user agent.
		 *
		 * @since 4.0
		 *
		 * @param string  $agent Request user agent.
		 * @param Request $this  Request instance.
		 */
		$this->agent = apply_filters( 'dd4t3_request_agent', $agent, $this );
	}

	/**
	 * Set current URL from the request.
	 *
	 * We will use REQUEST_URI value from the server.
	 * This should be called by constructor method only.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_url() {
		// phpcs:ignore
		$url = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';

		/**
		 * Filter hook to modify current url for request object.
		 *
		 * @since 4.0
		 *
		 * @param string  $url  Current URL.
		 * @param Request $this Request instance.
		 */
		$this->url = apply_filters( 'dd4t3_request_url', $url, $this );
	}

	/**
	 * Set current request IP address.
	 *
	 * Even if the IP masking is enabled we will try to get
	 * the IP address. But we won't store or show it anywhere.
	 * This should be called by constructor method only.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function set_ip() {
		/**
		 * Filter hook to short circuit IP checking.
		 *
		 * If you return false here, we will skip IP address finding.
		 * Use this filter instead of `dd4t3_request_ip` filter to avoid
		 * unnecessary IP findings.
		 *
		 * @since 4.0
		 *
		 * @param bool $check Should check for IP.
		 */
		$ip = apply_filters( 'dd4t3_request_set_ip', true );

		if ( $ip ) {
			foreach ( $this->ip_headers() as $header ) {
				if ( ! empty( $_SERVER[ $header ] ) ) {
					$ip = $_SERVER[ $header ]; // phpcs:ignore
					$ip = explode( ',', $ip );
					$ip = array_shift( $ip );
					break;
				}
			}

			// Convert to binary.
			$ip = @inet_pton( trim( $ip ) ); // phpcs:ignore
			if ( false !== $ip ) {
				// Convert back to string.
				$ip = @inet_ntop( $ip );  // phpcs:ignore
			}
		}

		if ( empty( $ip ) ) {
			$ip = '';
		}

		/**
		 * Filter hook to modify current request IP address.
		 *
		 * @since 4.0
		 *
		 * @param string  $ip   Current IP address.
		 * @param Request $this Request instance.
		 */
		$this->ip = apply_filters( 'dd4t3_request_ip', $ip, $this );
	}

	/**
	 * Set current request header values.
	 *
	 * We will ignore a few of the sensitive items.
	 * This should be called by constructor method only.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function set_headers() {
		// Ignored headers list.
		$ignored = $this->ignored_headers();

		$headers = array();

		foreach ( $_SERVER as $name => $value ) {
			if ( substr( $name, 0, 5 ) === 'HTTP_' ) {
				$name = strtolower( substr( $name, 5 ) );
				$name = str_replace( '_', ' ', $name );
				$name = ucwords( $name );
				$name = str_replace( ' ', '-', $name );

				// Skip ignored items.
				if ( ! in_array( strtolower( $name ), $ignored, true ) ) {
					$headers[ $name ] = $value;
				}
			}
		}

		/**
		 * Filter hook to modify current request headers.
		 *
		 * @since 4.0
		 *
		 * @param array   $headers Redirect headers.
		 * @param Request $this    Request instance.
		 */
		$this->headers = apply_filters( 'dd4t3_request_headers', $headers, $this );
	}

	/**
	 * Set current request extra data.
	 *
	 * Use the filter to add additional data if needed.
	 * This should be called by constructor method only.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function set_others() {
		$others = array(
			'protocol' => is_ssl() ? 'https' : 'http',
		);

		/**
		 * Filter hook to modify current request extra data.
		 *
		 * Use this filter to add or remove data.
		 *
		 * @since 4.0
		 *
		 * @param array   $others Redirect extra data.
		 * @param Request $this   Request instance.
		 */
		$this->others = apply_filters( 'dd4t3_request_others', $others, $this );
	}

	/**
	 * Set custom redirect data for the request.
	 *
	 * This should be called after setting up all request details.
	 * This should be called by constructor method only while creating
	 * new instance.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function set_redirect() {
		// Get matching redirect.
		$redirect = Redirects::instance()->get_by_source(
			$this->get_url()
		);

		/**
		 * Filter hook to modify current request redirect data.
		 *
		 * @since 4.0
		 *
		 * @param object|false $redirect Redirect data.
		 * @param Request      $this     Request instance.
		 */
		$this->redirect = apply_filters( 'dd4t3_request_redirect', $redirect, $this );
	}

	/**
	 * Set the matching log data for the current URL.
	 *
	 * Data will be set only if current request is a 404 request.
	 * This should be called after setting up all request details.
	 * This should be called by constructor method only while creating
	 * new instance.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_log() {
		// Only if a 404.
		if ( $this->is_404() ) {
			// Get log.
			$log = Logs::instance()->get_by_url(
				$this->get_url()
			);

			/**
			 * Filter hook to modify current request log data.
			 *
			 * @since 4.0
			 *
			 * @param object|false $log  Log data.
			 * @param Request      $this Request instance.
			 */
			$this->log = apply_filters( 'dd4t3_request_log', $log, $this );
		}
	}

	/**
	 * Get the list of ignored header items.
	 *
	 * Use filter to ignore more items.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	private function ignored_headers() {
		$ignored = array( 'cookie', 'host' );

		/**
		 * Filter to ignore header items from the list.
		 *
		 * @since 4.0
		 *
		 * @param array   $ignored Ignored list.
		 * @param Request $this    Request instance.
		 */
		return apply_filters( 'dd4t3_request_ignored_headers', $ignored, $this );
	}

	/**
	 * Get the list of request header names for IP.
	 *
	 * These are the header items we use to get IP address.
	 * If any of these values are available, we will stop checking.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	private function ip_headers() {
		$headers = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'HTTP_VIA',
			'REMOTE_ADDR',
		);

		/**
		 * Filter hook to add or remove header items for IP address.
		 *
		 * @since 4.0
		 *
		 * @param array   $headers Header item names.
		 * @param Request $this    Request instance.
		 */
		return apply_filters( 'dd4t3_request_ip_headers', $headers, $this );
	}

	/**
	 * Check if current request is 404.
	 *
	 * We are using WP's core function is_404() to check this.
	 * You can use the filter to do an additional check.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_404() {
		/**
		 * Filter hook to modify 404 check.
		 *
		 * @since 4.0.0
		 *
		 * @param bool $is_404 Is current request a 404.
		 */
		return apply_filters( 'dd4t3_request_is_404', is_404() );
	}

	/**
	 * Get a single item from the data object.
	 *
	 * If name is not given, entire data will be retuned back.
	 *
	 * @since 4.0
	 *
	 * @param object      $data    Data to process.
	 * @param bool|string $name    Name of the item.
	 * @param bool        $default Default value.
	 *
	 * @return object|mixed
	 */
	private function get_item( $data, $name = false, $default = false ) {
		// If asking for specific item.
		if ( ! empty( $name ) ) {
			return is_object( $data ) && property_exists( $data, $name ) ? $data->$name : $default;
		}

		return $data;
	}
}
