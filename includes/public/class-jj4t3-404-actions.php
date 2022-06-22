<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * The main 404 actions class.
 *
 * This class contains all functions performed during
 * each 404 errors on the site.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage Actions
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */
class JJ4T3_404_Actions extends JJ4T3_404_Data {

	/**
	 * Redirect url for current 404.
	 *
	 * @var    string
	 * @access public
	 *
	 */
	public $redirect_url = '';

	/**
	 * Custom redirect url for current 404.
	 *
	 * @var    string
	 * @access public
	 *
	 */
	public $custom_redirect_url = '';

	/**
	 * Redirect status code for currenr 404.
	 *
	 * @var    string
	 * @access public
	 * @since  3.0.0
	 */
	public $redirect_type = 301;

	/**
	 * Is redirect enabled for current 404.
	 *
	 * @var    boolean
	 * @access public
	 * @since  3.0.0
	 */
	public $redirect_enabled = false;

	/**
	 * Is logging enabled for current 404.
	 *
	 * @var    boolean
	 * @access public
	 * @since  3.0.0
	 */
	public $log_enabled = false;

	/**
	 * Is email alert enabled for current 404.
	 *
	 * @var    boolean
	 * @access public
	 * @since  3.0.0
	 */
	public $alert_enabled = false;

	/**
	 * Is common check passed for current 404.
	 *
	 * @var    boolean
	 * @access public
	 * @since  3.0.0
	 */
	public $common_check_passed = false;

	/**
	 * Initialize the class and parent class.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function __construct() {

		// Main filter that handles 404.
		add_action( 'template_redirect', array( $this, 'handle_404' ) );
		add_filter( 'redirect_canonical', array( $this, 'url_guessing' ) );
	}

	/**
	 * Perform 404 actions.
	 *
	 * Perform required actions on each 404 pages being visited.
	 * Log error details, Alert via email, Redirect.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function handle_404() {

		// Only if we can.
		if ( ! is_404() || is_admin() ) {
			return;
		}

		// Let's try folks.
		try {

			// Initialize.
			$this->init();

			// Set options for current 404.
			$this->set_options();

			// Log error details to database.
			$this->log_error();

			// Send email alert about the error.
			$this->email_alert();

			// Redirect the user.
			$this->redirect();

		} catch ( Exception $ex ) {
			// Who cares?
		}
	}

	/**
	 * Send email about the error.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function email_alert() {

		/**
		 * Filter to completely disable email alerts.
		 *
		 * @since 3.0.0
		 */
		if ( ! apply_filters( 'jj4t3_can_email_alert', $this->alert_enabled ) ) {
			return;
		}


		// Email alert class.
		$email = new JJ4T3_404_Email( $this );
		$email->send_email();
	}

	/**
	 * Log details of error to the database.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function log_error() {

		// Only if we can.
		if ( ! $this->log_enabled ) {
			return;
		}

		// Error logging class.
		$logging = new JJ4T3_404_Logging( $this );
		$logging->log_error();
	}

	/**
	 * Redirect 404 requests.
	 *
	 * If a 404 page is requested, take visitors to a proper existing page.
	 * Registering new action hook "jj4t3_before_redirect".
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @global object $wpdb WordPress DB object.
	 *
	 * @return void
	 */
	public function redirect() {

		// Only if we can.
		if ( ! $this->redirect_enabled ) {
			return;
		}

		if ( ! empty( $this->redirect_url ) ) {

			/**
			 * Action hook to perform before redirect.
			 *
			 * @since 3.0.0
			 *
			 * @param string $url Link to redirect.
			 */
			do_action( 'jj4t3_before_redirect', $this->redirect_url );

			// Perform redirect using WordPress.
			wp_redirect( $this->redirect_url, $this->redirect_type );
			// Exit, because WordPress will not exit automatically.
			exit;
		}
	}

	/**
	 * Set custom redirect url if set
	 *
	 * If custom redirect url is set for give 404 path,
	 * set that link.
	 * Registering filter "jj4t3_custom_redirect_url".
	 *
	 * @since  2.2.0
	 * @access private
	 *
	 * @global object $wpdb WP DB object
	 * @return void
	 */
	private function set_options() {
		if ( empty( $this->url ) ) {
			return;
		}

		global $wpdb;
		// Make sure that the errors are hidden.
		$wpdb->hide_errors();

		// Get custom redirect if set.
		$result = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT redirect, options FROM %1$s WHERE url = \'%2$s\' AND redirect IS NOT NULL LIMIT 0,1',
				JJ4T3_TABLE,
				$this->url
			)
		);

		$options = empty( $result->options ) ? array() : maybe_unserialize( $result->options );

		// Set all properties.
		$this->set_common_check();
		$this->set_redirect_url( $result );
		$this->set_redirect_type( $options );
		$this->set_redirect_status( $options );
		$this->set_logging_status( $options );
		$this->set_alert_status( $options );
	}

	/**
	 * Get url to redirect to.
	 *
	 * This function is used to get the url
	 * for redirecting to.
	 * If a custom redirect is set through admin dashboard or even through
	 * the filter "jj4t3_custom_redirect_url" for the current 404 page, it will be prioratised.
	 * Otherwise global redirect link.
	 * Registering filter - jj4t3_redirect_url.
	 *
	 * @param object $options Current 404 options.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_redirect_url( $options ) {

		$url = false;

		$custom_redirect = empty( $options->redirect ) ? '' : $options->redirect;

		/**
		 * Filter for modify/set current 404's custom redirect.
		 *
		 * Using this filter you can modify or set custom redirect
		 * for any 404 path.
		 * @note : If you want to remove custom redirect for a path, you can
		 * use this filter and return an empty/false value.
		 * If you have set a value here, this will get priority over global redirect.
		 *
		 * @since 3.0.0
		 */
		$custom_redirect = apply_filters( 'jj4t3_custom_redirect_url', $custom_redirect, $this->url );

		if ( ! empty( $custom_redirect ) ) {
			$url = esc_url( $custom_redirect );
			$this->custom_redirect_url = esc_url( $custom_redirect );
		} else {
			// Get redirect to.
			$to = jj4t3_get_option( 'redirect_to' );
			if ( 'page' === $to ) {
				// If an existing page is selected, get permalink.
				$url = get_permalink( jj4t3_get_option( 'redirect_page' ) );
			} elseif ( 'link' === $to ) {
				// If a link.
				$url = jj4t3_get_option( 'redirect_link' );
			}
		}

		/**
		 * Filter hook to change redirect url.
		 *
		 * To alter redirect link. Return full absolute
		 * path to redirect.
		 *
		 * @since 3.0.0
		 */
		$this->redirect_url = esc_url( apply_filters( 'jj4t3_redirect_url', $url ) );
	}

	/**
	 * Set redirect type for the current 404.
	 *
	 * This function is used to set the redirect type code
	 * for the current 404.
	 * Custom config for the 404 is considered first.
	 *
	 * @param object $options Current 404 options.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function set_redirect_type( $options ) {

		if ( isset( $options['type'] ) && is_numeric( $options['type'] ) ) {
			$this->redirect_type = intval( $options[ 'type' ] );
		} else {
			$this->redirect_type = jj4t3_redirect_type();
		}
	}

	/**
	 * Set if we can log 404 errors to database.
	 *
	 * This function is used to check and verify
	 * if the error logging is set to enabled.
	 * Checking custom config for the 404 first.
	 *
	 * @param object $options Current 404 options.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function set_logging_status( $options ) {

		if ( isset( $options['log'] ) && in_array( $options['log'], array( 0, 1 ) ) ) {
			$enabled = boolval( $options[ 'log' ] );
		} else {
			$enabled = jj4t3_log_enabled();
		}

		if ( $enabled && jj4t3_is_human() && $this->common_check_passed ) {
			$this->log_enabled = true;
		}
	}

	/**
	 * Set if we can email notify on errors.
	 *
	 * This function is used to check and verify
	 * if the email notification is enabled.
	 * Checking custom config for the 404 first.
	 *
	 * @param object $options Current 404 options.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function set_alert_status( $options ) {

		if ( isset( $options['alert'] ) && in_array( $options['alert'], array( 0, 1 ) ) ) {
			$enabled = boolval( $options[ 'alert' ] );
		} else {
			$enabled = jj4t3_email_notify_enabled();
		}

		if ( $enabled && jj4t3_is_human() && $this->common_check_passed ) {
			$this->alert_enabled = true;
		}
	}

	/**
	 * Set if we can perform redirect related actions.
	 *
	 * Verify that the common check passed.
	 * Verify that redirect is enabled by user (custom if any).
	 *
	 * @param object $options Current 404 options.
	 *
	 * @since  2.2.0
	 * @access public
	 *
	 * @return void
	 */
	private function set_redirect_status( $options ) {

		if ( isset( $options['redirect'] ) && in_array( $options['redirect'], array( 0, 1 ) ) ) {
			$enabled = boolval( $options['redirect'] );
		} else {
			$enabled = jj4t3_redirect_enabled();
		}

		if ( $enabled && $this->common_check_passed ) {
			$this->redirect_enabled = true;
		}
	}

	/**
	 * Set if the common checks are passed.
	 *
	 * Verify that the current page is not excluded.
	 * Verify that the current page is not an BuddyPress page
	 * only if BuddyPress is active.
	 *
	 * @since  2.2.0
	 * @access public
	 *
	 * @return void
	 */
	private function set_common_check() {

		// Do not redirect if excluded by user.
		if ( $this->is_excluded() ) {
			$this->common_check_passed = false;
		} elseif ( function_exists( 'bp_current_component' ) ) {
			$this->common_check_passed = ( ! bp_current_component() );
		} else {
			$this->common_check_passed = true;
		}
	}

	/**
	 * Disable URL guessing if enabled.
	 *
	 * @param bool $guess Current status.
	 *
	 * @since 3.0.4
	 *
	 * @return bool
	 */
	public function url_guessing( $guess ) {
		// Check if guessing is disabled.
		$disable_guessing = jj4t3_get_option( 'disable_guessing' );
		// Disable only on 404.
		if ( $disable_guessing && is_404() && ! isset( $_GET['p'] ) ) {
			$guess = false;
		}

		return $guess;
	}
}
