<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * Main 404 to 301 plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 * This class creates a one and only instance of 404 to 301 plugin.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage Core
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */
final class JJ_404_to_301 {

	/**
	 * Main 404 to 301 class instance.
	 *
	 * @var    JJ_404_to_301
	 * @since  3.0.0
	 * @access private
	 */
	private static $instance;

	/**
	 * Main JJ_404_to_301 Instance.
	 *
	 * Insures that only one instance of JJ_404_to_301 exists in memory
	 * at any one time.
	 * Also prevents needing to define globals all over the place.
	 *
	 * @since     3.0.0
	 * @access    public
	 * @staticvar array  $instance
	 *
	 * @return JJ_404_to_301|object
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof JJ_404_to_301 ) ) {

			// Main plugin class.
			self::$instance = new JJ_404_to_301();

			// Include required files.
			self::$instance->includes();
			// Load translation support.
			self::$instance->locale();

			// Required only when admin.
			if ( is_admin() ) {
				// Init admin class..
				self::$instance->admin = new JJ4T3_Admin();
			}

			// Required only when not admin.
			if ( ! is_admin() ) {
				// Init 404 class.
				self::$instance->actions = new JJ4T3_404_Actions();
			}
		}

		return self::$instance;
	}

	/**
	 * Include plugin's required files.
	 *
	 * Load all required files for this plugin's
	 * perfect functionality.
	 * We will handle the conditional checks inside
	 * these files.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function includes() {

		include_once JJ4T3_DIR . 'includes/functions/jj4t3-general-functions.php';
		include_once JJ4T3_DIR . 'includes/class-jj4t3-i18n.php';

		// Required only when not admin.
		if ( ! is_admin() ) {
			include_once JJ4T3_DIR . 'includes/public/class-jj4t3-404-data.php';
			include_once JJ4T3_DIR . 'includes/public/class-jj4t3-404-email.php';
			include_once JJ4T3_DIR . 'includes/public/class-jj4t3-404-logging.php';
			include_once JJ4T3_DIR . 'includes/public/class-jj4t3-404-actions.php';
		}

		// Required only when backend.
		if ( is_admin() ) {
			include_once JJ4T3_DIR . 'includes/admin/class-jj4t3-admin.php';
			include_once JJ4T3_DIR . 'includes/admin/class-jj4t3-log-listing.php';
		}
	}

	/**
	 * Initialize internationalization class.
	 *
	 * Load text domain for the internationalization functionality.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return object Locale class object.
	 */
	private function locale() {

		return new JJ4T3_I18n();
	}
}
