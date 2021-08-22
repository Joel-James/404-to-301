<?php
/**
 * The plugin menu controller class.
 *
 * This class handles the admin menu functionality for the plugin.
 * Thanks to Redirection (wordpress.org/plugins/redirection/)
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Controller
 * @subpackage Menu
 */

namespace DuckDev\Redirect\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Menu
 *
 * @package DuckDev\Redirect
 * @since   4.0.0
 */
class Request {

	/**
	 * Current request URL.
	 *
	 * @var string $url
	 *
	 * @since 4.0
	 */
	private $url = '';

	/**
	 * Current request method.
	 *
	 * @var string $method
	 *
	 * @since 4.0
	 */
	private $method = 'GET';

	/**
	 * Current request referer.
	 *
	 * @var string $referrer
	 *
	 * @since 4.0
	 */
	private $referrer = '';

	/**
	 * Current request IP address.
	 *
	 * @var string $ip
	 *
	 * @since 4.0
	 */
	private $ip = '';

	/**
	 * Current request user agent.
	 *
	 * @var string $agent
	 *
	 * @since 4.0
	 */
	private $agent = '';

	/**
	 * Get current request headers.
	 *
	 * @var array $headers
	 *
	 * @since 4.0
	 */
	private $headers = array();

	/**
	 * Get current request's custom config.
	 *
	 * @var array $headers
	 *
	 * @since 4.0
	 */
	private $config = array();

	/**
	 * Initialize assets functionality.
	 *
	 * @since 3.2.4
	 *
	 * @return void
	 */
	public function __construct() {
		$this->set_ip();
		$this->set_url();
		$this->set_agent();
		$this->set_method();
		$this->set_config();
		$this->set_referer();
		$this->set_headers();
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return string
	 */
	public function get_referer() {
		return $this->referrer;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return string
	 */
	public function get_agent() {
		return $this->agent;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return string
	 */
	public function get_ip() {
		return $this->ip;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	public function get_headers() {
		return $this->headers;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @param string $name    Name of the config.
	 * @param bool   $default Default value.
	 *
	 * @since  4.0
	 *
	 * @return array|mixed
	 */
	public function get_config( $name = '', $default = false ) {
		// If asking for specific config.
		if ( ! empty( $name ) ) {
			return isset( $this->config[ $name ] ) ? $this->config[ $name ] : $default;
		}

		return $this->config;
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_config() {
		$config = array(
			'log'             => 'global',
			'redirect'        => 'global',
			'email'           => 'global',
			'redirect_type'   => 301,
			'redirect_target' => '',
		);

		// @todo Get options from db.
		$options = array();

		// Merge with default config.
		$config = wp_parse_args( $options, $config );

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
		$this->config = apply_filters( 'dd4t3_request_config', $config );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_method() {
		$method = '';

		if ( isset( $_SERVER['REQUEST_METHOD'] ) ) {
			$method = $_SERVER['REQUEST_METHOD']; // phpcs:ignore
		}

		/**
		 * Filter hook to add or remove redirect types.
		 *
		 * Other plugins can use this filter to add new redirect
		 * types to 404 to 301.
		 *
		 * @param array $types Redirect types.
		 *
		 * @since 4.0
		 */
		$this->method = apply_filters( 'dd4t3_request_method', $method );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_referer() {
		$referrer = '';

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$referrer = $_SERVER['HTTP_REFERER']; // phpcs:ignore
		}

		/**
		 * Filter hook to add or remove redirect types.
		 *
		 * Other plugins can use this filter to add new redirect
		 * types to 404 to 301.
		 *
		 * @param array $types Redirect types.
		 *
		 * @since 4.0
		 */
		$this->referrer = apply_filters( 'dd4t3_request_referer', $referrer );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_agent() {
		$agent = '';

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$referrer = $_SERVER['HTTP_USER_AGENT']; // phpcs:ignore
		}

		/**
		 * Filter hook to add or remove redirect types.
		 *
		 * Other plugins can use this filter to add new redirect
		 * types to 404 to 301.
		 *
		 * @param array $types Redirect types.
		 *
		 * @since 4.0
		 */
		$this->agent = apply_filters( 'dd4t3_request_agent', $agent );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_url() {
		$url = '';

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = $_SERVER['REQUEST_URI']; // phpcs:ignore
		}

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
		$this->url = apply_filters( 'dd4t3_request_url', $url );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return void
	 */
	private function set_ip() {
		$ip = '';

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

		if ( empty( $ip ) ) {
			$ip = '';
		}

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
		$this->ip = apply_filters( 'dd4t3_request_ip', $ip );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
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
		 * Filter hook to add add or remove redirect types.
		 *
		 * Other plugins can use this filter to add new redirect
		 * types to 404 to 301.
		 *
		 * @param array $types Redirect types.
		 *
		 * @since 4.0
		 */
		$this->headers = apply_filters( 'dd4t3_request_headers', $headers );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	private function ignored_headers() {
		$ignored = array( 'cookie', 'host' );

		/**
		 * Filter to ignore header items from the list.
		 *
		 * @param array $ignored Ignored list.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd4t3_request_ignored_headers', $ignored );
	}

	/**
	 * Get available redirect types.
	 *
	 * Use `dd4t3_redirect_types` filter to add
	 * new redirect type.
	 *
	 * @since  4.0
	 *
	 * @return array
	 */
	private function ip_headers() {
		// Sub page.
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
		 * Filter hook to add add or remove redirect types.
		 *
		 * Other plugins can use this filter to add new redirect
		 * types to 404 to 301.
		 *
		 * @param array $types Redirect types.
		 *
		 * @since 4.0
		 */
		return apply_filters( 'dd4t3_request_ip_headers', $headers );
	}
}
