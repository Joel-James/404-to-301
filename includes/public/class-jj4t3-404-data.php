<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * The 404 error data class.
 *
 * This class set all required information about the current
 * 404 page. This class can be extended to access the 404
 * page details such as URL, Time, User Agent etc.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage 404Data
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */
class JJ4T3_404_Data {

	/**
	 * Visitor IP address.
	 *
	 * @var    string
	 * @access public
	 */
	public $ip = '';

	/**
	 * Visitor user agent.
	 *
	 * @var    string
	 * @access public
	 */
	public $ua = '';

	/**
	 * Referring link.
	 *
	 * @var    string
	 * @access public
	 */
	public $ref = 'n/a';

	/**
	 * Current requested path.
	 *
	 * @var    array
	 * @access public
	 */
	public $url = '';

	/**
	 * Current date and time.
	 *
	 * @var    array
	 * @access public
	 */
	public $time = '';

	/**
	 * Initialize the class.
	 *
	 * @since  3.0.0
	 * @access private
	 */
	public function init() {

		$this->set_ip();
		$this->set_ref();
		$this->set_ua();
		$this->set_url();
		$this->set_time();
	}

	/**
	 * Set visitors IP address.
	 *
	 * Get real IP address of the user.
	 * http://stackoverflow.com/a/55790/3845839
	 *
	 * @param string $ip Default value for IP Address.
	 *
	 * @since  2.2.6
	 * @access private
	 *
	 * @return void
	 */
	private function set_ip( $ip = '' ) {

		// IP varibals in priority oder.
		$ips = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
		foreach ( $ips as $ip ) {
			if ( isset( $_SERVER[ $ip ] ) ) {
				$ip = $_SERVER[ $ip ];
			}
		}

		/**
		 * Filter to alter visitors IP address.
		 *
		 * @since 3.0.0
		 */
		$this->ip = apply_filters( 'jj4t3_404_ip', $ip );
	}

	/**
	 * Set visitors user agent/browser.
	 *
	 * @param string $ua Default value for User Agent.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_ua( $ua = '' ) {

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$ua = $_SERVER['HTTP_USER_AGENT'];
		}

		/**
		 * Filter to alter User Agent.
		 *
		 * @since 3.0.0
		 */
		$this->ua = apply_filters( 'jj4t3_404_ua', $ua );
	}

	/**
	 * Set visitors referring link.
	 *
	 * @param string $ref Default value for Ref.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_ref( $ref = '' ) {

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$ref = esc_url( $_SERVER['HTTP_REFERER'] );
		}

		/**
		 * Filter to alter referrer url.
		 *
		 * To alter the url where the visitor comes from.
		 *
		 * @since 3.0.0
		 */
		$this->ref = apply_filters( 'jj4t3_404_ref', $ref );
	}

	/**
	 * Set visitors referring link.
	 *
	 * @param string $url Default value for 404 URL.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_url( $url = '' ) {

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = untrailingslashit( esc_url( $_SERVER['REQUEST_URI'] ) );
		}

		/**
		 * Filter to alter current 404 path.
		 *
		 * It is not recommended to change this value.
		 *
		 * @since 3.0.0
		 */
		$this->url = apply_filters( 'jj4t3_404_url', $url );
	}

	/**
	 * Set current time.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_time() {

		/**
		 * Filter to alter current time.
		 *
		 * @note If you using this filter, remember to
		 *  return proper MySQL time format.
		 *
		 * @since 3.0.0
		 */
		$this->time = apply_filters( 'jj4t3_404_time', current_time( 'mysql' ) );
	}

	/**
	 * Exclude specified paths from 404.
	 *
	 * If paths entered in exclude paths option is
	 * found in current 404 page, skip this from
	 * 404 actions.
	 *
	 * @since  2.0.8
	 * @access private
	 *
	 * @return boolean
	 */
	public function is_excluded() {

		$excluded = jj4t3_get_option( 'exclude_paths', '' );

		$paths = array();

		// If no exclude path set, return false early.
		if ( ! empty( $excluded ) ) {
			// Split by line break.
			$paths = explode( "\n", $excluded );
		}

		/**
		 * Filter to alter exclude path values.
		 *
		 * @note You should return array if strings .
		 *
		 * @since 3.0.0
		 */
		$paths = apply_filters( 'jj4t3_404_excluded_paths', $paths );

		// If split failed, return false.
		if ( empty( $paths ) ) {
			return false;
		}

		// Verify that the excluded path is not matching current page.
		foreach ( $paths as $path ) {
			if ( strpos( $this->url, trim( $path ) ) !== false ) {
				return true;
			}
		}

		return false;
	}

}
