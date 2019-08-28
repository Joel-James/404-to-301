<?php

namespace DuckDev\WP404\Controllers\Common;

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

use DuckDev\WP404\Utils\Abstracts\Base;

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
	 * @var    string $text_domain The text domain of the plugin.
	 * @since  4.0.0
	 * @access protected
	 */
	private $text_domain = DD404_SLUG;

	/**
	 * Initialize the class by registering the hooks.
	 *
	 * @since 4.0.0
	 */
	public function init() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
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
