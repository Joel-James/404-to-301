<?php
/**
 * Plugin identity and URL helpers.
 *
 * Holds the small set of constants and helpers that describe the
 * plugin to the outside world (slug, page slugs, admin URLs, admin
 * screen IDs).
 *
 * The class is intentionally static — there is only one plugin, so
 * carrying around an instance just to read its name would be noise.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
class Plugin {

	/**
	 * Plugin slug.
	 *
	 * Matches the WordPress.org slug and is used as a prefix for
	 * options, REST namespaces and admin page slugs.
	 *
	 * @since 4.0.0
	 */
	const SLUG = '404-to-301';

	/**
	 * Top-level admin menu slug (the Logs page).
	 *
	 * @since 4.0.0
	 */
	const PAGE_LOGS = '404-to-301-logs';

	/**
	 * Redirects sub-page slug.
	 *
	 * @since 4.0.0
	 */
	const PAGE_REDIRECTS = '404-to-301-redirects';

	/**
	 * Settings sub-page slug.
	 *
	 * @since 4.0.0
	 */
	const PAGE_SETTINGS = '404-to-301-settings';

	/**
	 * Addons sub-page slug.
	 *
	 * @since 4.0.0
	 */
	const PAGE_ADDONS = '404-to-301-addons';

	/**
	 * Admin screen IDs of every plugin page.
	 *
	 * Keyed by short name so callers can look up either the ID or the
	 * URL without having to remember how WordPress prefixes screen IDs
	 * for top-level vs sub-menu pages.
	 *
	 * @since 4.0.0
	 *
	 * @var array<string, string>
	 */
	private static $screens = array(
		'redirects' => 'toplevel_page_404-to-301-redirects',
		'logs'      => 'redirects_page_404-to-301-logs',
		'settings'  => 'redirects_page_404-to-301-settings',
		'addons'    => 'redirects_page_404-to-301-addons',
	);

	/**
	 * Get the plugin slug.
	 *
	 * Matches the wp.org slug and is used as the text domain.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function slug(): string {
		return self::SLUG;
	}

	/**
	 * Get the human-readable plugin name.
	 *
	 * Intentionally not translated — used in places where the brand
	 * name needs to stay consistent (admin menu, plugin row).
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function name(): string {
		return '404 to 301';
	}

	/**
	 * Get the current plugin version.
	 *
	 * Resolves to whatever the `Version:` header in the main plugin
	 * file declares (loaded into `D404_VERSION` at boot).
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function version(): string {
		return D404_VERSION;
	}

	/**
	 * Get every plugin admin screen ID.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string, string> Map of short name => screen ID.
	 */
	public static function screens(): array {
		return self::$screens;
	}

	/**
	 * Get every plugin admin page as { id, url, slug } triples.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string, array{id: string, url: string, slug: string}>
	 */
	public static function pages(): array {
		$pages = array();

		foreach ( self::$screens as $key => $id ) {
			$slug          = '404-to-301-' . $key;
			$pages[ $key ] = array(
				'id'   => $id,
				'url'  => admin_url( 'admin.php?page=' . $slug ),
				'slug' => $slug,
			);
		}

		return $pages;
	}

	/**
	 * Get the admin URL of one of the plugin pages.
	 *
	 * @since 4.0.0
	 *
	 * @param string $page Page key (logs, settings, redirects, addons).
	 *
	 * @return string Absolute admin URL, or an empty string for an unknown page key.
	 */
	public static function get_url( string $page = 'settings' ): string {
		$pages = self::pages();

		return $pages[ $page ]['url'] ?? '';
	}

	/**
	 * Build the admin URL of the settings page.
	 *
	 * Convenience accessor used by plugin row links and CTAs.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function settings_url(): string {
		return self::get_url( 'settings' );
	}

	/**
	 * Get the admin screen ID of a plugin page.
	 *
	 * @since 4.0.0
	 *
	 * @param string $page Page key (logs, settings, redirects, addons).
	 *
	 * @return string
	 */
	public static function screen_id( string $page = 'logs' ): string {
		return self::$screens[ $page ] ?? '';
	}

	/**
	 * Whether the current admin screen is one of the plugin's pages.
	 *
	 * @since 4.0.0
	 *
	 * @param string|null $page Optional page key to match against.
	 *
	 * @return bool
	 */
	public static function is_plugin_screen( ?string $page = null ): bool {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! $screen || empty( $screen->id ) ) {
			return false;
		}

		if ( null === $page ) {
			return in_array( $screen->id, self::$screens, true );
		}

		return self::screen_id( $page ) === $screen->id;
	}
}
