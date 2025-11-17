<?php
/**
 * The plugin cache class.
 *
 * This class contains the functionality to manage the object cache
 * and transients for our plugin.
 * Extending helper class to override prefix.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Core
 * @subpackage Cache
 */

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Cache
 *
 * @since   4.0.0
 * @extends \DuckDev\Cache\Cache
 * @package DuckDev\FourNotFour
 */
class Cache extends \DuckDev\Cache\Cache {

	/**
	 * The prefix for all our keys.
	 *
	 * @since  4.0.0
	 * @var string $prefix
	 * @access protected
	 */
	protected $prefix = '404_to_301_cache';
}
