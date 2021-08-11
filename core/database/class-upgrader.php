<?php
/**
 * The upgrade process class.
 *
 * This class handles the upgrade processes using background processing.
 *
 * @since      1.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Upgrade
 * @subpackage Upgrade
 */

namespace DuckDev\Redirect\Database;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Upgrades\Process;
use DuckDev\Upgrades\Upgrader as Base;

/**
 * Class Upgrade.
 *
 * @since   1.0.0
 * @extends Base
 * @package DuckDev\Redirect\Database
 */
class Upgrader extends Base {

	/**
	 * Holds the name of the background process action.
	 *
	 * @var    string
	 * @since  1.0.0
	 * @access protected
	 */
	protected $action = 'duckdev_upgrade';

	/**
	 * Get the available upgrade processes.
	 *
	 * All plugins should override this method and return the
	 * upgrade processes.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return Process[] Array of processes.
	 */
	protected function get_upgrades() {
		return array(
			'v4_settings' => Upgrades\V4_Settings::get(),
		);
	}
}
