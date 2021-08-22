<?php
/**
 * The log item class.
 *
 * This class will should format a single log item.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database\Rows
 * @subpackage Log
 */

namespace DuckDev\Redirect\Database\Rows;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Row;

/**
 * Class Log.
 *
 * @since   4.0.0
 * @extends Row
 * @package DuckDev\Redirect\Database\Rows
 */
class Log extends Row {

	/**
	 * Log item constructor.
	 *
	 * @param mixed $item Item data.
	 *
	 * @since  4.0.0
	 * @access public
	 */
	public function __construct( $item ) {
		parent::__construct( $item );

		// Set the type of each column, and prepare.
		$this->id         = (int) $this->id;
		$this->url        = (string) $this->url;
		$this->referrer   = (string) $this->referrer;
		$this->ip         = (string) $this->ip;
		$this->agent      = (string) $this->agent;
		$this->method     = (string) $this->method;
		$this->request    = (array) $this->request;
		$this->created_at = strtotime( $this->created_at );
	}
}