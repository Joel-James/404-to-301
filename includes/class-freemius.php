<?php
/**
 * Freemius integration wrapper.
 *
 * Wraps `duckdev/freemius-plugin-licensing` so the rest of the plugin
 * doesn't have to know its constructor signature. Two roles:
 *
 *   - **Parent plugin client** — the Freemius client representing 404
 *     to 301 itself. Exposed via `addon()` / `license()` / `update()`
 *     and used to fetch the remote addon catalogue.
 *
 *   - **Per-addon clients** — each premium addon has its own Freemius
 *     project ID and public key; `for_addon()` returns (and memoises)
 *     a Freemius client for that addon so its license can be
 *     activated/deactivated independently.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\Freemius\Freemius as Client;
use DuckDev\Freemius\Services\Addon as AddonService;
use DuckDev\Freemius\Services\License as LicenseService;
use DuckDev\Freemius\Services\Update as UpdateService;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Freemius
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
class Freemius extends Singleton {

	/**
	 * Default Freemius plugin id for 404 to 301. Replace with the
	 * real id once the project is registered. A `0` here means "no
	 * remote catalogue" — the addons UI falls back to a built-in
	 * stub list.
	 *
	 * @since 4.0.0
	 */
	const PLUGIN_ID = 2192;

	/**
	 * Default Freemius public key (the non-secret half of the pair).
	 *
	 * @since 4.0.0
	 */
	const PUBLIC_KEY = 'pk_9d470f3128e5e491ea5a2da6bf4bf';

	/**
	 * Parent-plugin Freemius client (404 to 301).
	 *
	 * @since 4.0.0
	 * @var Client|null
	 */
	private $client;

	/**
	 * Memoised per-addon Freemius clients, keyed by Freemius project ID.
	 *
	 * @since 4.0.0
	 * @var array<int, Client>
	 */
	private $addon_clients = array();

	/**
	 * Build the parent client if a plugin id has been configured.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		$id   = $this->plugin_id();
		$args = $this->plugin_args();

		if ( $id > 0 && class_exists( Client::class ) ) {
			$this->client = Client::get_instance( $id, $args );
		}
	}

	/**
	 * Whether the parent client is configured and ready.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_ready(): bool {
		return $this->client instanceof Client;
	}

	/**
	 * Get the parent Freemius addon service.
	 *
	 * @since 4.0.0
	 *
	 * @return AddonService|null
	 */
	public function addon(): ?AddonService {
		return $this->client ? $this->client->addon() : null;
	}

	/**
	 * Get the parent Freemius license service.
	 *
	 * @since 4.0.0
	 *
	 * @return LicenseService|null
	 */
	public function license(): ?LicenseService {
		return $this->client ? $this->client->license() : null;
	}

	/**
	 * Get the parent Freemius update service.
	 *
	 * @since 4.0.0
	 *
	 * @return UpdateService|null
	 */
	public function update(): ?UpdateService {
		return $this->client ? $this->client->update() : null;
	}

	/**
	 * Get (or build) a Freemius client for a specific addon.
	 *
	 * Each premium addon has its own Freemius project; this method
	 * memoises one client per project id so repeated calls reuse the
	 * same instance.
	 *
	 * @since 4.0.0
	 *
	 * @param int   $id   Addon's Freemius project id.
	 * @param array $args {
	 *     Args forwarded to the Freemius client.
	 *
	 *     @type string $slug        Addon slug.
	 *     @type string $main_file   Plugin basename, when locally installed.
	 *     @type string $public_key  Addon's Freemius public key.
	 *     @type bool   $is_premium  Whether the addon is premium.
	 *     @type bool   $has_addons  Almost always false for an addon.
	 * }
	 *
	 * @return Client|null Null when Freemius isn't available or id is invalid.
	 */
	public function for_addon( int $id, array $args ): ?Client {
		if ( $id <= 0 || ! class_exists( Client::class ) ) {
			return null;
		}

		if ( isset( $this->addon_clients[ $id ] ) ) {
			return $this->addon_clients[ $id ];
		}

		$defaults = array(
			'slug'       => '',
			'main_file'  => '',
			'public_key' => '',
			'is_premium' => true,
			'has_addons' => false,
		);

		$this->addon_clients[ $id ] = Client::get_instance( $id, wp_parse_args( $args, $defaults ) );

		return $this->addon_clients[ $id ];
	}

	/**
	 * Get the Freemius plugin id for the parent plugin.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	private function plugin_id(): int {
		/**
		 * Filter the Freemius plugin id used by 404 to 301.
		 *
		 * @since 4.0.0
		 *
		 * @param int $id Default id.
		 */
		return (int) apply_filters( '404_to_301_freemius_plugin_id', self::PLUGIN_ID );
	}

	/**
	 * Build the args array passed into the parent Freemius client.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	private function plugin_args(): array {
		$defaults = array(
			'slug'       => Plugin::SLUG,
			'is_premium' => false,
			'has_addons' => true,
			'main_file'  => D404_BASE_NAME,
			'public_key' => self::PUBLIC_KEY,
		);

		/**
		 * Filter the parent Freemius client args.
		 *
		 * @since 4.0.0
		 *
		 * @param array $args Default args.
		 */
		return (array) apply_filters( '404_to_301_freemius_args', $defaults );
	}
}
