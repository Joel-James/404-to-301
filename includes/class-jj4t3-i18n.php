<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @category   Core
 * @package    JJ4T3
 * @subpackage Internationalization
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301/
 */
class JJ4T3_i18n {

	/**
	 * Initialize the class.
	 *
	 * @since  3.0.0
	 * @access private
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function textdomain() {

		load_plugin_textdomain( '404-to-301', false, JJ4T3_DIR . '/languages/' );
	}

}
