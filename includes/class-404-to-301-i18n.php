<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://iscode.co/products/404-to-301/
 * @since      2.0.7
 * @package    I4T3
 * @subpackage I4T3/includes
 * @author     Joel James <me@joelsays.com>
 */
class _404_To_301_i18n {
	/**
	 * The domain specified for this plugin.
	 *
	 * @since    2.0.7
	 * @access   private
	 * @var      string    $domain    The domain identifier for this plugin.
	 */
	private $domain;
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.7
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since    2.0.7
	 * @param    string    $domain    The domain that represents the locale of this plugin.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}
}