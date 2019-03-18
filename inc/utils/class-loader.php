<?php

namespace DuckDev404\Inc\Utils;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Register all actions and filters for the plugin
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 */
class Loader {

	/**
	 * Registers an action hook.
	 *
	 * @param string $tag            The name of the action to which the $method is hooked.
	 * @param string $method         The name of the method to be called.
	 * @param int    $priority       Optional. Used to specify the order in which the
	 *                               functions associated with a particular action are executed
	 *                               (default: 10). Lower numbers correspond with earlier execution,
	 *                               and functions with the same priority are executed in the order in
	 *                               which they were added to the action.
	 * @param int    $accepted_args  Optional. The number of arguments the function
	 *                               accept (default 1).
	 *
	 * @since 4.0
	 * @uses  add_action() To register action hook.
	 *
	 * @return Loader
	 */
	protected function add_action( $tag, $method = '', $priority = 10, $accepted_args = 1 ) {
		add_action(
			$tag,
			$this->get_callback( $tag, $method ),
			$priority,
			$accepted_args
		);

		return $this;
	}

	/**
	 * Registers a filter hook.
	 *
	 * @param string $tag            The name of the filter to hook the $method to.
	 * @param string $method         The name of the method to be called when the
	 *                               filter is applied.
	 * @param int    $priority       Optional. Used to specify the order in which the
	 *                               functions associated with a particular action are executed
	 *                               (default: 10). Lower numbers correspond with earlier execution,
	 *                               and functions with the same priority are executed in the order in
	 *                               which they were added to the action.
	 * @param int    $accepted_args  Optional. The number of arguments the function
	 *                               accept (default 1).
	 *
	 * @since 4.0
	 * @uses  add_filter() To register filter hook.
	 *
	 * @return Loader
	 */
	protected function add_filter( $tag, $method = '', $priority = 10, $accepted_args = 1 ) {
		add_filter(
			$tag,
			$this->get_callback( $tag, $method ),
			$priority,
			$accepted_args
		);

		return $this;
	}

	/**
	 * Registers AJAX action hook.
	 *
	 * @param string  $tag      The name of the AJAX action to which the $method is
	 *                          hooked.
	 * @param string  $method   Optional. The name of the method to be called.
	 *                          If the name of the method is not provided, tag name will be used
	 *                          as method name.
	 * @param boolean $private  Optional. Determines if we should register hook
	 *                          for logged in users.
	 * @param boolean $public   Optional. Determines if we should register hook
	 *                          for not logged in users.
	 *
	 * @since 4.0
	 *
	 * @return Loader
	 */
	protected function add_ajax_action( $tag, $method = '', $private = true, $public = false ) {
		if ( $private ) {
			$this->run_action( 'wp_ajax_' . $tag, $method );
		}

		if ( $public ) {
			$this->run_action( 'wp_ajax_nopriv_' . $tag, $method );
		}

		return $this;
	}

	/**
	 * Executes the callback function instantly if the specified action was
	 * already fired. If the action was not fired yet then the action handler
	 * is registered via add_action().
	 *
	 * Important note:
	 * If the callback is executed instantly, then the functionr receives NO
	 * parameters!
	 *
	 * @param string $tag            The name of the filter to hook the $method to.
	 * @param string $method         The name of the method to be called when the
	 *                               filter is applied.
	 * @param int    $priority       Optional. Used to specify the order in which the
	 *                               functions associated with a particular action are executed
	 *                               (default: 10). Lower numbers correspond with earlier execution,
	 *                               and functions with the same priority are executed in the order in
	 *                               which they were added to the action.
	 * @param int    $accepted_args  Optional. The number of arguments the function
	 *                               accept (default 1).
	 *
	 * @since 4.0
	 * @usues add_action() To register action hook.
	 *
	 * @return Loader
	 */
	protected function run_action( $tag, $method = '', $priority = 10, $accepted_args = 1 ) {
		// Get the callback.
		$callback = $this->get_callback( $tag, $method );

		if ( did_action( $tag ) ) {
			// Note: No argument is passed to the callback!
			call_user_func( $callback );
		} else {
			add_action(
				$tag,
				$callback,
				$priority,
				$accepted_args
			);
		}

		return $this;
	}

	/**
	 * Returns the callback array for the specified method
	 *
	 * @param  string       $tag    The tag that is addressed by the callback.
	 * @param  string|array $method The callback method.
	 *
	 * @since 4.0
	 *
	 * @return array A working callback.
	 */
	private function get_callback( $tag, $method ) {
		if ( is_array( $method ) ) {
			return $method;
		} else {
			return array( $this, empty( $method ) ? $tag : $method );
		}
	}
}
