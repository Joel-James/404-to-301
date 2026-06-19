<?php
/**
 * Admin menu registration.
 *
 * Builds the 4-item menu under "404 to 301":
 *   Redirects (top level)
 *   ├ Redirects  (alias of top level)
 *   ├ 404 Logs
 *   ├ Settings
 *   └ Addons
 *
 * Each sub-menu callback delegates to {@see Page::render()} — the
 * admin pages are just React mount-points; the real UI lives in
 * `assets/src/`.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Plugin;
use DuckDev\FourNotFour\Utils\Permission;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Menu
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Admin
 */
class Menu extends Singleton {

	/**
	 * Register hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'admin_menu', array( $this, 'rename_top_level' ), 11 );

		// TEMP: "New" badge on the Add-ons submenu — remove in a future
		// release (both the action below and `badge_addons()` / its CSS).
		add_action( 'admin_menu', array( $this, 'badge_addons' ), 12 );
		add_action( 'admin_head', array( $this, 'badge_addons_style' ) );
	}

	/**
	 * Register every plugin menu entry.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		$cap = Permission::get_cap();

		// Top-level page — defaults to the Redirects view.
		add_menu_page(
			__( 'Custom Redirects — 404 to 301', '404-to-301' ),
			__( 'Redirects', '404-to-301' ),
			$cap,
			Plugin::PAGE_REDIRECTS,
			array( Page::instance(), 'render_redirects' ),
			'dashicons-redo',
			89
		);

		// Sub-menu pages, in display order.
		add_submenu_page(
			Plugin::PAGE_REDIRECTS,
			__( 'Custom Redirects', '404-to-301' ),
			__( 'Redirects', '404-to-301' ),
			$cap,
			Plugin::PAGE_REDIRECTS,
			array( Page::instance(), 'render_redirects' )
		);

		add_submenu_page(
			Plugin::PAGE_REDIRECTS,
			__( '404 Error Logs', '404-to-301' ),
			__( '404 Logs', '404-to-301' ),
			$cap,
			Plugin::PAGE_LOGS,
			array( Page::instance(), 'render_logs' )
		);

		add_submenu_page(
			Plugin::PAGE_REDIRECTS,
			__( '404 to 301 Settings', '404-to-301' ),
			__( 'Settings', '404-to-301' ),
			$cap,
			Plugin::PAGE_SETTINGS,
			array( Page::instance(), 'render_settings' )
		);

		add_submenu_page(
			Plugin::PAGE_REDIRECTS,
			__( '404 to 301 Add-ons', '404-to-301' ),
			__( 'Add-ons', '404-to-301' ),
			$cap,
			Plugin::PAGE_ADDONS,
			array( Page::instance(), 'render_addons' )
		);
	}

	/**
	 * Rename the top-level menu label to the brand name.
	 *
	 * WordPress uses the first sub-menu's label as the parent label
	 * by default. We register the parent with "Redirects" so the
	 * first submenu reads cleanly, then swap the parent label here.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function rename_top_level(): void {
		global $menu;

		if ( ! is_array( $menu ) ) {
			return;
		}

		foreach ( $menu as $position => $data ) {
			if ( isset( $data[2] ) && Plugin::PAGE_REDIRECTS === $data[2] ) {
				// Rewriting our own row's display label in the global
				// `$menu` is the documented WP way to override the
				// auto-derived top-level title (default is the first
				// submenu page's name). This is the only touch we make.
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$menu[ $position ][0] = Plugin::name();
				return;
			}
		}
	}

	/**
	 * Append a "New" badge to the Add-ons submenu label.
	 *
	 * TEMP: highlights the freshly expanded add-on catalogue. Remove this
	 * method (and its `admin_menu` / `admin_head` hooks in {@see init()},
	 * plus {@see badge_addons_style()}) in a future release once the
	 * catalogue is no longer "new".
	 *
	 * Appends to the displayed label only — the registered page/menu
	 * titles stay clean — mirroring how {@see rename_top_level()} edits
	 * the menu globals, and how WP core injects its own count bubbles.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function badge_addons(): void {
		global $submenu;

		if ( empty( $submenu[ Plugin::PAGE_REDIRECTS ] ) || ! is_array( $submenu[ Plugin::PAGE_REDIRECTS ] ) ) {
			return;
		}

		$badge = ' <span class="d404-menu-badge">' . esc_html__( 'New', '404-to-301' ) . '</span>';

		foreach ( $submenu[ Plugin::PAGE_REDIRECTS ] as $index => $item ) {
			if ( isset( $item[2] ) && Plugin::PAGE_ADDONS === $item[2] ) {
				// Append once — guard against a re-run leaving two badges.
				if ( false === strpos( (string) $item[0], 'd404-menu-badge' ) ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$submenu[ Plugin::PAGE_REDIRECTS ][ $index ][0] = $item[0] . $badge;
				}
				return;
			}
		}
	}

	/**
	 * Inline CSS for the Add-ons "New" badge.
	 *
	 * Emitted on `admin_head` (every screen) because the submenu is
	 * visible site-wide, whereas the plugin's stylesheet only enqueues on
	 * its own pages. TEMP — remove alongside {@see badge_addons()}.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function badge_addons_style(): void {
		?>
		<style id="d404-menu-badge-style">
			#adminmenu .d404-menu-badge {
				display: inline-block;
				margin-left: 5px;
				padding: 0 7px;
				border-radius: 9px;
				background: #d63638;
				color: #fff;
				font-size: 10px;
				font-weight: 600;
				line-height: 17px;
				text-transform: uppercase;
				letter-spacing: .3px;
			}
		</style>
		<?php
	}
}
