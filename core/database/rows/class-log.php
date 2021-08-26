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
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @var    string
	 * @access protected
	 * @since  4.0.0
	 */
	protected $prefix = '404_to_301';

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
		$this->id              = (int) $this->id;
		$this->url             = (string) $this->url;
		$this->referrer        = (string) $this->referrer;
		$this->ip              = (string) $this->ip;
		$this->agent           = (string) $this->agent;
		$this->request_method  = (string) $this->request_method;
		$this->request_data    = (string) $this->request_data;
		$this->visits          = (string) $this->visits;
		$this->redirect_status = (string) $this->redirect_status;
		$this->log_status      = (string) $this->log_status;
		$this->email_status    = (string) $this->email_status;
		$this->meta            = (string) $this->meta;
		$this->created_at      = strtotime( $this->created_at );
		$this->updated_at      = strtotime( $this->updated_at );
		$this->updated_by      = (int) $this->updated_by;
	}
}