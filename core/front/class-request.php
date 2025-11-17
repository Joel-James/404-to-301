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

namespace DuckDev\FourNotFour\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\FourNotFour\Models;
use Symfony\Component\HttpFoundation;

/**
 * Class Request
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front
 */
class Request {

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
	 * Get the symfony request instance.
	 *
	 * @var HttpFoundation\Request
	 */
	private $request;

	/**
	 * Create new request object.
	 *
	 * Please use DuckDev\FourNotFour\Front::get_request() method to
	 * get the current request object. Creating new instances every time
	 * is a bad idea. Do only if required.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Setup request.
		$this->request = HttpFoundation\Request::createFromGlobals();

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
	public function method() {
		$method = $this->request->getMethod();

		/**
		 * Filter hook to modify current request method.
		 *
		 * @since 4.0
		 *
		 * @param string  $method Request method.
		 * @param Request $this   Request instance.
		 */
		return apply_filters( '404_to_301_request_get_method', $method, $this );
	}

	/**
	 * Get current request referer.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function referer() {
		$referer = $this->request->headers->get( 'referer' );

		/**
		 * Filter hook to modify current request referer.
		 *
		 * @since 4.0
		 *
		 * @param string  $referrer Request referer.
		 * @param Request $this     Request instance.
		 */
		return apply_filters( '404_to_301_request_get_referer', $referer, $this );
	}

	/**
	 * Get current request user agent name.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function agent() {
		$agent = $this->request->headers->get( 'User-Agent' );

		/**
		 * Filter hook to modify current request user agent.
		 *
		 * @since 4.0
		 *
		 * @param string  $agent Request user agent.
		 * @param Request $this  Request instance.
		 */
		return apply_filters( '404_to_301_request_get_agent', $agent, $this );
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
	public function ip() {
		/**
		 * Filter hook to mask IP address.
		 *
		 * If you return true here, we will skip IP address finding.
		 *
		 * @since 4.0
		 *
		 * @param bool $mask Should mask.
		 */
		$mask = apply_filters( '404_to_301_request_mask_ip', false );

		if ( false === $mask ) {
			$ip = $this->request->getClientIp();
		} else {
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
		return apply_filters( '404_to_301_request_ip', $ip, $this );
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
	public function url() {
		$url = $this->request->getRequestUri();

		/**
		 * Filter hook to modify current url.
		 *
		 * @since 4.0
		 *
		 * @param string  $url  Current URL.
		 * @param Request $this Request instance.
		 */
		return apply_filters( '404_to_301_request_get_url', $url, $this );
	}

	/**
	 * Get current request host.
	 *
	 * This will be the domain name of request.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function host() {
		$host = esc_url_raw( $this->request->getHost() );

		/**
		 * Filter hook to modify current host.
		 *
		 * @since 4.0
		 *
		 * @param string  $url  Current host.
		 * @param Request $this Request instance.
		 */
		return apply_filters( '404_to_301_request_get_host', $host, $this );
	}

	/**
	 * Get current request scheme.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function scheme() {
		$protocol = is_ssl() ? 'https' : 'http';

		/**
		 * Filter hook to modify current request scheme.
		 *
		 * @since 4.0
		 *
		 * @param array   $header Request scheme (http or https).
		 * @param Request $this   Request instance.
		 */
		return apply_filters( '404_to_301_request_get_scheme', $protocol, $this );
	}

	/**
	 * Get current request header item value by name.
	 *
	 * @since 4.0
	 *
	 * @param string      $key     The header name.
	 * @param string|null $default The default value.
	 *
	 * @return string|null
	 */
	public function header( $key, $default = null ) {
		$header = $this->request->headers->get( $key, $default );

		/**
		 * Filter hook to modify current request header value.
		 *
		 * @since 4.0
		 *
		 * @param array   $header Request header value.
		 * @param Request $this   Request instance.
		 */
		return apply_filters( '404_to_301_request_get_header', $header, $this );
	}

	/**
	 * Get current request header items.
	 *
	 * Please note these may contain sensitive items.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function headers() {
		$headers = $this->request->headers->all();

		/**
		 * Filter hook to modify current request headers.
		 *
		 * @since 4.0
		 *
		 * @param array   $headers Request headers.
		 * @param Request $this    Request instance.
		 */
		return apply_filters( '404_to_301_request_get_headers', $headers, $this );
	}

	/**
	 * Get if any extra data available for current request.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function others() {
		$others = array(
			'host'   => $this->host(),
			'scheme' => $this->scheme(),
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
		return apply_filters( '404_to_301_request_get_others', $others, $this );
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
	public function get_info( $name = false, $default = false ) {
		return $this->get_item( $this->log, $name, $default );
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
		$redirect = Models\Redirects::instance()->get_by_source(
			$this->url()
		);

		/**
		 * Filter hook to modify current request redirect data.
		 *
		 * @since 4.0
		 *
		 * @param object|false $redirect Redirect data.
		 * @param Request      $this     Request instance.
		 */
		$this->redirect = apply_filters( '404_to_301_request_redirect', $redirect, $this );
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
			$log = Models\Logs::instance()->get_by_url(
				$this->url()
			);

			/**
			 * Filter hook to modify current request log data.
			 *
			 * @since 4.0
			 *
			 * @param object|false $log  Log data.
			 * @param Request      $this Request instance.
			 */
			$this->log = apply_filters( '404_to_301_request_log', $log, $this );
		}
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
		return apply_filters( '404_to_301_request_is_404', is_404() );
	}

	/**
	 * Get a single item from the data object.
	 *
	 * If name is not given, entire data will be returned back.
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
