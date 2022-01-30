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
 * @subpackage Redirect
 */

namespace DuckDev\Redirect\Database\Rows;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use BerlinDB\Database\Row;

/**
 * Class Redirect.
 *
 * @since   4.0.0
 * @extends Row
 *
 * @property int    $id          ID of the redirect.
 * @property string $source      Source path.
 * @property string $destination Destination link.
 * @property int    $code        Redirect code.
 * @property string $type        Redirect type.
 * @property string $status      Redirect status.
 * @property array  $meta        Meta data.
 * @property string $created_at  Created time.
 * @property string $updated_at  Updated time.
 * @property int    $created_by  Created user id.
 * @property int    $updated_by  Updated user id.
 *
 * @package DuckDev\Redirect\Database\Rows
 */
class Redirect extends Row {

	/**
	 * Global prefix used for tables/hooks/cache-groups/etc.
	 *
	 * @var    string
	 * @access protected
	 * @since  4.0.0
	 */
	protected $prefix = '404_to_301';

	/**
	 * Redirect item constructor.
	 *
	 * @param mixed $item Item data.
	 *
	 * @since  4.0.0
	 * @access public
	 */
	public function __construct( $item ) {
		parent::__construct( $item );

		// Set the type of each column, and prepare.
		$this->id          = (int) $this->id;
		$this->source      = (string) $this->source;
		$this->destination = (string) $this->destination;
		$this->code        = (int) $this->code;
		$this->type        = (string) $this->type;
		$this->meta        = (string) $this->meta;
		$this->status      = (string) $this->status;
		$this->created_at  = strtotime( $this->created_at );
		$this->updated_at  = strtotime( $this->updated_at );
		$this->created_by  = (int) $this->created_by;
		$this->updated_by  = (int) $this->updated_by;
	}
}