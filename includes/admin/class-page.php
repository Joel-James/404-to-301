<?php
/**
 * Admin page renderers.
 *
 * Each method outputs the matching React mount-point template. The
 * actual UI lives in `assets/src/{settings,logs,redirects,addons}.js`,
 * which are loaded by {@see Assets} on the matching screen only.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Page
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Admin
 */
class Page extends Singleton {

	/**
	 * Mount point ID used by the Logs page React app.
	 *
	 * @since 4.0.0
	 */
	const MOUNT_LOGS = '404-to-301-logs';

	/**
	 * Mount point ID used by the Redirects page React app.
	 *
	 * @since 4.0.0
	 */
	const MOUNT_REDIRECTS = '404-to-301-redirects';

	/**
	 * Mount point ID used by the Settings page React app.
	 *
	 * @since 4.0.0
	 */
	const MOUNT_SETTINGS = '404-to-301-settings';

	/**
	 * Mount point ID used by the Addons page React app.
	 *
	 * @since 4.0.0
	 */
	const MOUNT_ADDONS = '404-to-301-addons';

	/**
	 * Render the Logs admin page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_logs(): void {
		$this->render_template( 'logs', self::MOUNT_LOGS );
	}

	/**
	 * Render the Redirects admin page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_redirects(): void {
		$this->render_template( 'redirects', self::MOUNT_REDIRECTS );
	}

	/**
	 * Render the Settings admin page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_settings(): void {
		$this->render_template( 'settings', self::MOUNT_SETTINGS );
	}

	/**
	 * Render the Addons admin page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_addons(): void {
		$this->render_template( 'addons', self::MOUNT_ADDONS );
	}

	/**
	 * Locate and include a template under `/templates/`.
	 *
	 * The template receives one variable in scope, `$mount_id`, which
	 * is the DOM id the React app attaches to.
	 *
	 * @since 4.0.0
	 *
	 * @param string $name     Template name (no extension).
	 * @param string $mount_id Mount-point DOM id passed into the template.
	 *
	 * @return void
	 */
	private function render_template( string $name, string $mount_id ): void {
		$path = D404_DIR . 'templates/' . $name . '.php';

		if ( ! is_readable( $path ) ) {
			return;
		}

		include $path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	}
}
