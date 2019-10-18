<?php

namespace DuckDev\WP404\Models;

// Direct hit? You must die.
defined( 'WPINC' ) || die;

use WP_Error;
use DuckDev\WP404\Utils\Abstracts\Base;

/**
 * Single log item model class.
 *
 * @link       https://duckdev.com
 * @since      4.0.0
 * @package    Settings
 * @subpackage Endpoint
 *
 * @author     Joel James <me@joelsays.com>
 */
class Log_Item extends Base {

	/**
	 * API endpoint for the current endpoint.
	 *
	 * @var string $endpoint
	 *
	 * @since 4.0.0
	 */
	protected $id;

	/**
	 * Get the redirect type of current error.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function get_redirect_type() {
		// Send response.
		return 301;
	}

	/**
	 * Get the redirect url of current error.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function get_redirect_url() {
		// Send response.
		return 301;
	}

	/**
	 * Get the redirect type of current error.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function get_log_status() {
		// Send response.
		return true;
	}

	/**
	 * Get the redirect type of current error.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function get_email_status() {
		// Send response.
		return true;
	}

	/**
	 * Save the current log item to db.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function save() {
		// Send response.
		return true;
	}

	/**
	 * Delete the current log item.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function delete() {
		// Send response.
		return true;
	}
}
