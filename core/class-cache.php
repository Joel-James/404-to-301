<?php
/**
 * The plugin cache class.
 *
 * This class contains the functionality to manage the object cache
 * and transients for our plugin.
 * Extending helper class to override prefix.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Cache
 */

namespace DuckDev\Redirect;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Cache
 *
 * @since   4.0.0
 * @extends \DuckDev\Cache\Cache
 * @package DuckDev\Redirect
 */
class Cache extends \DuckDev\Cache\Cache {

	/**
	 * The prefix for all our keys.
	 *
	 * @var string $prefix
	 * @since 4.0.0
	 */
	protected $prefix = '404_to_301_cache';
}
