<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * The main 404 error logging class.
 *
 * This class logs the error data to the database.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage ErrorLogging
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */
class JJ4T3_404_Logging {

	/**
	 * Error data class object.
	 *
	 * @var    object
	 * @access public
	 * @since  3.0.0
	 */
	public $data;

	/**
	 * Initialize the class and set properties.
	 *
	 * @param object $error_data Error log data class.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function __construct( $error_data ) {

		$this->data = $error_data;
	}

	/**
	 * Log details of error to the database.
	 *
	 * Registered new action hook "jj4t3_before_logging".
	 *
	 * @since  3.0.0
	 * @access public
	 * @global object $wpdb WordPress database.
	 */
	public function log_error() {

		global $wpdb;

		$data = $this->get_data();

		/**
		 * Action hook before logging.
		 *
		 * To perform actions before logging errors to db.
		 *
		 * @since 3.0.0
		 *
		 * @param array $data Error log data.
		 */
		do_action( 'jj4t3_before_logging', $data );

		// Insert data to db.
		$wpdb->insert( JJ4T3_TABLE, $data );
	}

	/**
	 * Get error log data in proper format.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return array $data Array of error log data.
	 */
	private function get_data() {

		// Set error data fields.
		$data = array(
			'date' => $this->data->time,
			'ip' => $this->data->ip,
			'url' => $this->data->url,
			'ref' => $this->data->ref,
			'ua' => $this->data->ua,
		);

		// If a custom redirect is set.
		if ( $this->data->custom_redirect_url ) {
			$data['redirect'] = $this->data->custom_redirect_url;
		}

		return $data;
	}

}
