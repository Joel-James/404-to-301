<?php
/**
 * Admin page renderers.
 *
 * Each method outputs the matching React mount-point div. The actual
 * UI lives in `assets/src/{settings,logs,redirects,addons}.js`, which
 * are loaded by {@see Assets} on the matching screen only.
 *
 * The mount-point markup is literally one div per page, so we emit it
 * inline rather than load a template file — see {@see self::mount()}.
 *
 * @package DuckDev\FourNotFour
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
		$this->mount( self::MOUNT_LOGS );
	}

	/**
	 * Render the Redirects admin page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_redirects(): void {
		$this->mount( self::MOUNT_REDIRECTS );
	}

	/**
	 * Render the Settings admin page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_settings(): void {
		$this->mount( self::MOUNT_SETTINGS );
	}

	/**
	 * Render the Addons admin page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_addons(): void {
		$this->mount( self::MOUNT_ADDONS );
	}

	/**
	 * Emit the React mount-point div.
	 *
	 * Every admin screen rendered by this plugin is just an empty div
	 * the React app attaches to — printing the markup here is cheaper
	 * (and easier to audit) than including a one-line template file.
	 *
	 * @since 4.0.0
	 *
	 * @param string $mount_id DOM id the React app attaches to.
	 *
	 * @return void
	 */
	private function mount( string $mount_id ): void {
		printf(
			'<div id="%s" class="d404-wrap"></div>',
			esc_attr( $mount_id )
		);
	}
}
