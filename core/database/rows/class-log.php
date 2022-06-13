<?php
/**
 * The log item class.
 *
 * This class will should format a single log item.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
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
 *
 * @property int        $log_id               ID of the log.
 * @property string     $url                  404 URL path.
 * @property string     $referrer             Referral link.
 * @property string     $ip                   IP address.
 * @property string     $agent                User agent.
 * @property string     $request_method       Request method.
 * @property array      $request_data         Request data.
 * @property array|null $meta                 Meta data.
 * @property int        $visits               No. of visits.
 * @property string     $redirect_status      Redirect status.
 * @property string     $log_status           Log status.
 * @property string     $email_status         Email status.
 * @property int|null   $redirect_id          Redirect ID.
 * @property string     $created_at           Created time.
 * @property string     $updated_at           Updated time.
 * @property int        $updated_by           Updated user id.
 *
 * @package DuckDev\Redirect\Database\Rows
 */
class Log extends Row {

	/**
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @since  4.0.0
	 * @var    string
	 * @access protected
	 */
	protected $prefix = '404_to_301';

	/**
	 * Log item constructor.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param mixed $item Item data.
	 */
	public function __construct( $item ) {
		parent::__construct( $item );

		// Set the type of each column, and prepare.
		$this->log_id          = (int) $this->log_id;
		$this->url             = (string) $this->url;
		$this->referrer        = (string) $this->referrer;
		$this->ip              = (string) $this->ip;
		$this->agent           = (string) $this->agent;
		$this->request_method  = (string) $this->request_method;
		$this->request_data    = is_null( $this->request_data ) ? null : maybe_unserialize( $this->request_data );
		$this->meta            = is_null( $this->meta ) ? null : maybe_unserialize( $this->meta );
		$this->visits          = (int) $this->visits;
		$this->redirect_status = (string) $this->redirect_status;
		$this->log_status      = (string) $this->log_status;
		$this->email_status    = is_null( $this->email_status ) ? null : (string) $this->email_status;
		$this->redirect_id     = is_null( $this->redirect_id ) ? null : (int) $this->redirect_id;
		$this->created_at      = strtotime( $this->created_at );
		$this->updated_at      = is_null( $this->updated_at ) ? null : strtotime( $this->updated_at );
		$this->updated_by      = is_null( $this->updated_by ) ? null : (int) $this->updated_by;
	}
}