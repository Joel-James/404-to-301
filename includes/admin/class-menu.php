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
}
