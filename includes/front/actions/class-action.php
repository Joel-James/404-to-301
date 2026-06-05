<?php
/**
 * Abstract base for front-end actions.
 *
 * Implements {@see Actionable} and gives every concrete action two
 * small helpers: a `setting()` accessor that reads a plugin setting
 * with a fallback, and a no-op default `should_run()` that subclasses
 * can override for short-circuit checks.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Front\Actions;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Contracts\Actionable;
use DuckDev\FourNotFour\Core;
use DuckDev\FourNotFour\Front\Request;

/**
 * Class Action
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Front\Actions
 */
abstract class Action implements Actionable {

	/**
	 * Run the action.
	 *
	 * Implementations decide whether to skip via {@see Action::should_run()}.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return void
	 */
	abstract public function run( Request $request ): void;

	/**
	 * Whether the action should run for the given request.
	 *
	 * Default: always run. Subclasses override.
	 *
	 * @since 4.0.0
	 *
	 * @param Request $request Current request.
	 *
	 * @return bool
	 */
	protected function should_run( Request $request ): bool {
		return true;
	}

	/**
	 * Read a setting value with a fallback.
	 *
	 * Wraps the Core service locator so action subclasses don't need
	 * to remember the long namespace.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Fallback when the Settings service is
	 *                         not yet available or the key is missing.
	 *
	 * @return mixed
	 */
	protected function setting( string $key, $fallback = null ) {
		$settings = Core::instance()->settings();

		return $settings ? $settings->get( $key, $fallback ) : $fallback;
	}
}
