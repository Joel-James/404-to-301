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
 * @subpackage Option
 */

namespace DuckDev\Redirect\Database\Rows;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Row;

/**
 * Class Option.
 *
 * @since   4.0.0
 * @extends Row
 * @package DuckDev\Redirect\Database\Rows
 */
class Option extends Row {

	/**
	 * Option item constructor.
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
		$this->log_id          = (int) $this->log_id;
		$this->redirect_id     = (int) $this->redirect_id;
		$this->redirect_status = (string) $this->redirect_status;
		$this->log_status      = (string) $this->log_status;
		$this->email_status    = (string) $this->email_status;
		$this->created_at      = strtotime( $this->created_at );
		$this->updated_at      = strtotime( $this->updated_at );
		$this->updated_by      = (int) $this->updated_by;
	}
}