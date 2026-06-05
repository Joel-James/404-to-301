<?php
/**
 * Contract for front-end request actions.
 *
 * Each "thing the plugin does on a 404" — logging, email alerts,
 * redirects — is implemented as an Actionable. The {@see
 * \DuckDev\FourNotFour\Front\Controller} hands the current request
 * to a chain of Actionables and runs each one in turn.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Contracts;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Front\Request;

/**
 * Interface Actionable
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Contracts
 */
interface Actionable {

	/**
	 * Run the action for the current request.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return void
	 */
	public function run( Request $request ): void;
}
