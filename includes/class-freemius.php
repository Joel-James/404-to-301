<?php
/**
 * Freemius integration wrapper.
 *
 * Wraps `duckdev/freemius-plugin-licensing` (the same SDK Loggedin
 * uses) so the rest of the plugin doesn't have to know its
 * constructor signature or the layout of the underlying services.
 *
 * Two distinct roles in one class:
 *
 *   - **Parent plugin client** — the Freemius client representing
 *     404 to 301 itself. Built once in {@see Freemius::init()} from
 *     {@see Freemius::PLUGIN_ID} + {@see Freemius::PUBLIC_KEY}. Used
 *     to fetch the remote addon catalog and to manage the parent
 *     plugin's own license (we don't sell the parent itself today,
 *     but the wiring is in place for the future).
 *
 *   - **Per-addon clients** — each premium addon ships with its own
 *     Freemius project (separate id + public key). When the addon
 *     plugin loads, it registers itself through the
 *     `404_to_301_register_addon` filter; we then build a Freemius
 *     client for that addon on demand via
 *     {@see Freemius::for_addon()}. The client array is memoised
 *     per id so repeated calls reuse the same instance.
 *
 * The class is a {@see Singleton} so {@see Core} can hand out the
 * same instance everywhere (REST endpoint, React script vars, etc.).
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
use DuckDev\Freemius\Services\Service;
use DuckDev\Freemius\Services\Update as UpdateService;
use DuckDev\FourNotFour\Utils\Singleton;
use WP_Error;

/**
 * Class Freemius
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
class Freemius extends Singleton {

	/**
	 * Freemius project id for 404 to 301 itself.
	 *
	 * Passed as the `$id` to `Freemius::get_instance()`. Stays as a
	 * constant rather than a setting so it can't accidentally drift
	 * with a clobbered options table.
	 *
	 * @since 4.0.0
	 */
	const PLUGIN_ID = 2192;

	/**
	 * Freemius public key for 404 to 301 itself.
	 *
	 * Public half of the keypair — it's safe to ship in a public
	 * repository. The private half lives on the Freemius dashboard.
	 *
	 * @since 4.0.0
	 */
	const PUBLIC_KEY = 'pk_9d470f3128e5e491ea5a2da6bf4bf';

	/**
	 * The parent-plugin Freemius client (404 to 301 itself).
	 *
	 * Built once in {@see Freemius::init()}. Null when the SDK is
	 * not available (eg. composer dependencies missing) — every
	 * accessor that returns a sub-service guards against that.
	 *
	 * @since 4.0.0
	 *
	 * @var Client|null
	 */
	private $client;

	/**
	 * Memoised per-addon Freemius clients.
	 *
	 * Keyed by Freemius project id so a single lookup serves any
	 * license / update operation against a specific addon.
	 *
	 * @since 4.0.0
	 *
	 * @var array<int, Client>
	 */
	private $addon_clients = array();

	/**
	 * Build the parent client.
	 *
	 * Runs once on the first `Freemius::instance()` call. No remote
	 * calls happen here — the SDK only talks to Freemius when an
	 * operation explicitly asks for it (catalog refresh, license
	 * activation, etc.).
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		if ( ! class_exists( Client::class ) ) {
			return;
		}

		$this->client = Client::get_instance(
			$this->plugin_id(),
			$this->plugin_args()
		);
	}

	/**
	 * Whether the parent Freemius client is configured and ready.
	 *
	 * Returns false when the SDK is missing entirely, or when the
	 * id / public key resolve to empty values (the early-development
	 * state before the project is registered on Freemius).
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_ready(): bool {
		return $this->client instanceof Client;
	}

	// --------------------------------------------------------------------- //
	// Parent client — short-hand accessors mirroring the SDK shape.
	// --------------------------------------------------------------------- //

	/**
	 * Get the parent client's addon service.
	 *
	 * Used to talk to the catalog endpoint
	 * (`get_addons( bool $force )`).
	 *
	 * @since 4.0.0
	 *
	 * @return AddonService|null Null when the SDK isn't initialised.
	 */
	public function addon(): ?AddonService {
		return $this->client ? $this->client->addon() : null;
	}

	/**
	 * Get the parent client's license service.
	 *
	 * @since 4.0.0
	 *
	 * @return LicenseService|null
	 */
	public function license(): ?LicenseService {
		return $this->client ? $this->client->license() : null;
	}

	/**
	 * Get the parent client's update service.
	 *
	 * @since 4.0.0
	 *
	 * @return UpdateService|null
	 */
	public function update(): ?UpdateService {
		return $this->client ? $this->client->update() : null;
	}

	// --------------------------------------------------------------------- //
	// Per-addon clients.
	// --------------------------------------------------------------------- //

	/**
	 * Get (or lazily build) the Freemius client for a specific addon.
	 *
	 * Each premium addon ships with its own Freemius project id and
	 * public key. The args array supplies those two values plus the
	 * regular Freemius `slug` / `main_file` / `is_premium` metadata.
	 *
	 * Calling this twice with the same id returns the memoised
	 * instance — important because the SDK keeps its own cache
	 * keyed by id, and we want both sides to agree.
	 *
	 * @since 4.0.0
	 *
	 * @param int   $id   Freemius project id of the addon (> 0).
	 * @param array $args {
	 *     Args forwarded to `Freemius::get_instance()`.
	 *
	 *     @type string $slug        Addon slug (matches the addon's
	 *                               plugin folder / wp.org slug).
	 *     @type string $main_file   Plugin basename, when locally
	 *                               installed (eg. `my-addon/my-addon.php`).
	 *     @type string $public_key  Addon's Freemius public key.
	 *     @type bool   $is_premium  Whether the addon is premium.
	 *     @type bool   $has_addons  Almost always false for an addon.
	 * }
	 *
	 * @return Client|null Null when the SDK isn't available or the
	 *                     id is invalid.
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

		$this->addon_clients[ $id ] = Client::get_instance(
			$id,
			wp_parse_args( $args, $defaults )
		);

		return $this->addon_clients[ $id ];
	}

	// --------------------------------------------------------------------- //
	// Catalog + license helpers (data + actions inspired by Loggedin).
	// --------------------------------------------------------------------- //

	/**
	 * Fetch the remote addon catalog.
	 *
	 * Reads from the parent client's `Addon` service which talks to
	 * the Freemius API and caches the response for 24 hours (the
	 * SDK manages that transient itself). Pass `$force = true` to
	 * bypass the cache, eg. when the user clicks "Refresh".
	 *
	 * @since 4.0.0
	 *
	 * @param bool $force Whether to force a fresh API request.
	 *
	 * @return array<int, array> Catalog rows, exactly as the SDK
	 *                           returns them. Empty when the SDK
	 *                           isn't configured.
	 */
	public function get_addons( bool $force = false ): array {
		$service = $this->addon();

		return $service ? (array) $service->get_addons( $force ) : array();
	}

	/**
	 * Get the list of addons that have registered themselves with us.
	 *
	 * Addon plugins announce themselves via the
	 * `404_to_301_register_addon` filter on `plugins_loaded`:
	 *
	 *     add_filter( '404_to_301_register_addon', function ( $addons ) {
	 *         $addons[ 12345 ] = array(
	 *             'slug'       => 'my-addon',
	 *             'is_premium' => true,
	 *             'main_file'  => 'my-addon/my-addon.php',
	 *             'public_key' => 'pk_...',
	 *         );
	 *         return $addons;
	 *     } );
	 *
	 * An addon being in this list means its plugin is installed and
	 * active locally — that's the signal the Addons UI uses to flip
	 * its "Active" badge on and reveal the license input.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array> Keyed by Freemius project id.
	 */
	public function get_registered_addons(): array {
		/**
		 * Filter the list of addons registered with 404 to 301.
		 *
		 * Each entry is keyed by Freemius project id and the value
		 * is the args array forwarded to `Freemius::get_instance()`.
		 *
		 * @since 4.0.0
		 *
		 * @param array<int, array> $addons Registered addons.
		 */
		return (array) apply_filters( '404_to_301_register_addon', array() );
	}

	/**
	 * Get the per-addon license state for every registered addon.
	 *
	 * Walks the registered-addons list and, for each one, builds (or
	 * fetches) the addon's Freemius client and pulls its activation
	 * data out of the local options table. The returned shape is the
	 * one the Addons React UI consumes:
	 *
	 *     [
	 *         12345 => [
	 *             'key'     => 'xxxx-xxxx-xxxx-xxxx',
	 *             'status'  => 'activated',         // or 'deactivated' / ''
	 *             'active'  => true,                // shortcut for `status === 'activated'`
	 *         ],
	 *         ...
	 *     ]
	 *
	 * Addons whose Freemius client we couldn't build (missing public
	 * key, etc.) are skipped silently.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array{key:string,status:string,active:bool}>
	 */
	public function get_license_items(): array {
		$items = array();

		foreach ( $this->get_registered_addons() as $id => $args ) {
			$id     = (int) $id;
			$client = $this->for_addon( $id, (array) $args );

			if ( ! $client ) {
				continue;
			}

			$license    = $client->license();
			$activation = $license ? (array) $license->get_activation_data() : array();
			$status     = (string) ( $activation['status'] ?? '' );

			$items[ $id ] = array(
				'key'    => (string) ( $activation['activation_params']['license_key'] ?? '' ),
				'status' => $status,
				'active' => Service::ACTIVATED === $status,
			);
		}

		return $items;
	}

	/**
	 * Activate a license key for the given addon.
	 *
	 * Looks the addon up in the registered-addons filter so we use
	 * the right `public_key` / `slug` when building the client. If
	 * the addon isn't registered (i.e. the addon plugin isn't
	 * installed / active), the activation can't succeed —
	 * activation has to happen on the same site that holds the
	 * private key the SDK uses to sign the request.
	 *
	 * @since 4.0.0
	 *
	 * @param int    $id  Freemius project id of the addon.
	 * @param string $key License key to activate.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function activate_license( int $id, string $key ) {
		$client = $this->resolve_addon_client( $id );

		if ( ! $client ) {
			return new WP_Error(
				'addon_not_registered',
				__( 'This add-on is not registered or its license public key is missing.', '404-to-301' )
			);
		}

		$license = $client->license();

		if ( ! $license ) {
			return new WP_Error(
				'license_service_unavailable',
				__( 'License service is not available for this add-on.', '404-to-301' )
			);
		}

		return $license->activate( $key );
	}

	/**
	 * Deactivate the license for the given addon.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id Freemius project id of the addon.
	 *
	 * @return bool|array|WP_Error True / WP_Error from the SDK.
	 */
	public function deactivate_license( int $id ) {
		$client = $this->resolve_addon_client( $id );

		if ( ! $client ) {
			return new WP_Error(
				'addon_not_registered',
				__( 'This add-on is not registered.', '404-to-301' )
			);
		}

		$license = $client->license();

		if ( ! $license ) {
			return new WP_Error(
				'license_service_unavailable',
				__( 'License service is not available for this add-on.', '404-to-301' )
			);
		}

		return $license->deactivate();
	}

	// --------------------------------------------------------------------- //
	// Internals.
	// --------------------------------------------------------------------- //

	/**
	 * Build (or fetch the memoised copy of) a per-addon client by id,
	 * pulling the args out of the registered-addons filter.
	 *
	 * Returns null when the id isn't registered — license operations
	 * can't proceed without the addon's own `public_key`, which lives
	 * in the filter payload.
	 *
	 * @since 4.0.0
	 *
	 * @param int $id Freemius project id.
	 *
	 * @return Client|null
	 */
	private function resolve_addon_client( int $id ): ?Client {
		$registered = $this->get_registered_addons();

		if ( ! isset( $registered[ $id ] ) ) {
			return null;
		}

		return $this->for_addon( $id, (array) $registered[ $id ] );
	}

	/**
	 * Get the Freemius plugin id for the parent plugin, after the
	 * `404_to_301_freemius_plugin_id` filter has run.
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
	 * Build the args array passed into the parent Freemius client,
	 * after the `404_to_301_freemius_args` filter has run.
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
			'main_file'  => D404_FILE,
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
