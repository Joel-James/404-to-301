<?php
/**
 * The plugin core.
 *
 * Boots every subsystem (common services, admin, front-end, REST API,
 * WP-CLI) in the right order and exposes a small service-locator
 * surface so callers have a single, well-known place to reach the
 * long-lived services.
 *
 * The class is intentionally `final` — the boot sequence is the source
 * of truth, and subclassing it would let third-party code reorder the
 * sequence in ways that are very hard to debug.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Core
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
final class Core extends Singleton {

	/**
	 * Boot the plugin.
	 *
	 * Wires every subsystem up in dependency order: common services
	 * first, then admin, front-end, REST and WP-CLI. Each subsystem is
	 * responsible for registering its own WordPress hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		$this->common();
		$this->admin();
		$this->front();
		$this->api();
		$this->cli();

		/**
		 * Fires once the plugin is fully loaded.
		 *
		 * Addons should hook into this action so they only initialise
		 * when the parent plugin is active and ready.
		 *
		 * @since 4.0.0
		 *
		 * @param Core $core Plugin core instance.
		 */
		do_action( '404_to_301_init', $this );
	}

	// --------------------------------------------------------------------- //
	// Service locator.
	// --------------------------------------------------------------------- //

	/**
	 * Get the shared {@see Settings} instance.
	 *
	 * Wired up in P3; the accessor is declared here so addons can rely
	 * on the public surface from day one.
	 *
	 * @since 4.0.0
	 *
	 * @return Settings|null Null until Settings is wired up in P3.
	 */
	public function settings(): ?Settings {
		return class_exists( __NAMESPACE__ . '\\Settings' ) ? Settings::instance() : null;
	}

	// --------------------------------------------------------------------- //
	// Boot helpers — subsystems wired up phase-by-phase.
	// --------------------------------------------------------------------- //

	/**
	 * Initialise services and hooks that run on every request.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function common(): void {
		// Database — wired up in P2.
		if ( class_exists( __NAMESPACE__ . '\\Database\\Database' ) ) {
			Database\Database::instance();
		}

		// Settings — wired up in P3.
		if ( class_exists( __NAMESPACE__ . '\\Settings' ) ) {
			Settings::instance();
		}

		// Upgrader — runs version-bump migrations on every load.
		if ( class_exists( __NAMESPACE__ . '\\Setup\\Upgrader' ) ) {
			Setup\Upgrader::instance();
		}

		// Migrator — wires the AS / cron callbacks so migration chunks
		// fire even when the admin is away.
		if ( class_exists( __NAMESPACE__ . '\\Migration\\Migrator' ) ) {
			Migration\Migrator::instance();
		}

		// Freemius — addon catalog + license + update channel.
		if ( class_exists( __NAMESPACE__ . '\\Freemius' ) ) {
			Freemius::instance();
		}
	}

	/**
	 * Get the shared {@see Freemius} wrapper.
	 *
	 * @since 4.0.0
	 *
	 * @return Freemius|null
	 */
	public function freemius(): ?Freemius {
		return class_exists( __NAMESPACE__ . '\\Freemius' ) ? Freemius::instance() : null;
	}

	/**
	 * Initialise the admin-side classes.
	 *
	 * Only runs inside `wp-admin` so the front-end never pays for the
	 * admin code path.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function admin(): void {
		if ( ! is_admin() ) {
			return;
		}

		// Wired up in P5.
		if ( class_exists( __NAMESPACE__ . '\\Admin\\Menu' ) ) {
			Admin\Menu::instance();
		}
		if ( class_exists( __NAMESPACE__ . '\\Admin\\Assets' ) ) {
			Admin\Assets::instance();
		}
		if ( class_exists( __NAMESPACE__ . '\\Admin\\Links' ) ) {
			Admin\Links::instance();
		}
		if ( class_exists( __NAMESPACE__ . '\\Admin\\Site_Health' ) ) {
			Admin\Site_Health::instance();
		}
	}

	/**
	 * Initialise the front-end controller.
	 *
	 * Only runs outside `wp-admin`.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function front(): void {
		if ( is_admin() ) {
			$settings = $this->settings();
			$track    = $settings && $settings->get( 'track_admin_404', false );

			if ( ! $track ) {
				return;
			}
		}

		// Wired up in P4.
		if ( class_exists( __NAMESPACE__ . '\\Front\\Controller' ) ) {
			Front\Controller::instance();
		}
	}

	/**
	 * Register the REST API endpoints.
	 *
	 * Endpoints are intentionally `new`'d rather than singletoned: they
	 * hold no per-request state, so a fresh instance per boot is fine
	 * and keeps testing trivial.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function api(): void {
		// Wired up in P3, P6, P7, P8, P9.
		foreach ( array( 'Settings', 'Logs', 'Redirects', 'Addons', 'Migration' ) as $endpoint ) {
			$class = __NAMESPACE__ . '\\Api\\' . $endpoint;
			if ( class_exists( $class ) ) {
				new $class();
			}
		}
	}

	/**
	 * Register the WP-CLI commands when WP-CLI is available.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function cli(): void {
		if ( ! ( defined( 'WP_CLI' ) && \WP_CLI ) || ! class_exists( '\\WP_CLI' ) ) {
			return;
		}

		// Wired up in P10.
		if ( class_exists( __NAMESPACE__ . '\\CLI\\CLI' ) ) {
			CLI\CLI::register();
		}
	}
}
