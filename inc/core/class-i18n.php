<?php

namespace DuckDev404\Inc\Core;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
class I18n extends Base {

	/**
	 * The text domain of the plugin.
	 *
	 * @since  4.0
	 * @access protected
	 * @var    string $text_domain The text domain of the plugin.
	 */
	private $text_domain = '404-to-301';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 4.0
	 */
	public function init() {
		$this->add_action( 'plugins_loaded', 'load_textdomain' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			$this->text_domain,
			false,
			DD404_DIR . '/languages/'
		);
	}

}
