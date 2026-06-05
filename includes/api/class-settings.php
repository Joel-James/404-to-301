<?php
/**
 * Settings import/export REST endpoint.
 *
 * Lets admins (and the React UI) export the entire user-facing
 * settings object as a JSON envelope, and apply that envelope back
 * onto another site for staging-to-prod sync.
 *
 * The day-to-day `GET /settings` + `PATCH /settings` round-trip is
 * already served by core's `/wp/v2/settings` bridge (set up by
 * {@see \DuckDev\FourNotFour\Settings::register()}) — this class is
 * deliberately scoped to the import/export workflow so the two paths
 * don't compete for the same route.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Settings as SettingsStore;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Settings
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Api
 */
class Settings extends Endpoint {

	/**
	 * Envelope schema version. Bumped only when the shape of the JSON
	 * payload changes in a way the importer needs to be aware of.
	 *
	 * @since 4.0.0
	 */
	const ENVELOPE_VERSION = 1;

	/**
	 * Keys that are scoped to a specific install and must never round-
	 * trip through an export/import cycle. `plugin_version` is excluded
	 * because the envelope already carries it at the top level; the
	 * others track installer/migration state that's meaningless on a
	 * different site.
	 *
	 * @since 4.0.0
	 * @var string[]
	 */
	const INTERNAL_KEYS = array(
		'plugin_version',
		'db_version',
		'logs_migrated',
		'phase1_done',
		'legacy_table_dropped',
	);

	/**
	 * Register routes.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/settings/export',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'export' ),
					'permission_callback' => array( $this, 'require_access' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/settings/import',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'import' ),
					'permission_callback' => array( $this, 'require_access' ),
					'args'                => array(
						'settings' => array(
							'type'        => 'object',
							'required'    => true,
							'description' => 'Settings payload — the `settings` object from a prior export envelope.',
						),
					),
				),
			)
		);
	}

	/**
	 * GET /settings/export.
	 *
	 * Returns a JSON envelope describing the current settings. The
	 * React UI turns the body into a downloadable file; CLI consumers
	 * can pipe the body straight into a file.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function export( WP_REST_Request $request ): WP_REST_Response {
		unset( $request ); // No args.

		$settings = SettingsStore::instance()->all();

		foreach ( self::INTERNAL_KEYS as $key ) {
			unset( $settings[ $key ] );
		}

		$envelope = array(
			'plugin'         => '404-to-301',
			'schema_version' => self::ENVELOPE_VERSION,
			'plugin_version' => defined( 'D404_VERSION' ) ? D404_VERSION : '',
			'exported_at'    => gmdate( 'c' ),
			'site_url'       => home_url(),
			'settings'       => $settings,
		);

		/**
		 * Filter the settings-export envelope before it's returned.
		 *
		 * Addons that store their own settings inside the plugin's
		 * option get carried automatically — they share the same
		 * `settings` payload. Addons that store state elsewhere can
		 * append to the envelope here.
		 *
		 * @since 4.0.0
		 *
		 * @param array $envelope The export envelope.
		 */
		$envelope = (array) apply_filters( '404_to_301_settings_export', $envelope );

		return $this->respond( $envelope );
	}

	/**
	 * POST /settings/import.
	 *
	 * Accepts an object — either the full envelope or just its
	 * `settings` payload — and applies it to the current site. The
	 * write goes through {@see SettingsStore::update()} so every key
	 * is sanitised by the existing pipeline before it lands on disk;
	 * unknown keys are dropped by `sanitize()` rather than rejected,
	 * so an envelope produced by a newer plugin version downgrades
	 * gracefully.
	 *
	 * Per-install state ({@see self::INTERNAL_KEYS}) is stripped from
	 * the incoming payload — importing those would clobber
	 * installer/migration state on the destination site.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function import( WP_REST_Request $request ) {
		$body = $request->get_param( 'settings' );

		if ( ! is_array( $body ) ) {
			return new WP_Error(
				'rest_invalid_payload',
				__( 'Invalid import payload — expected an object.', '404-to-301' ),
				array( 'status' => 400 )
			);
		}

		// Accept either the raw settings object (caller pre-stripped
		// the envelope) or the full envelope (caller posted the file
		// contents verbatim). The presence of a `plugin` key is the
		// signal that we're holding an envelope.
		$incoming = isset( $body['plugin'] ) && isset( $body['settings'] ) && is_array( $body['settings'] )
			? $body['settings']
			: $body;

		foreach ( self::INTERNAL_KEYS as $key ) {
			unset( $incoming[ $key ] );
		}

		/**
		 * Filter the imported payload before it's merged.
		 *
		 * @since 4.0.0
		 *
		 * @param array $incoming Sanitised candidate settings.
		 * @param array $body     Raw request body.
		 */
		$incoming = (array) apply_filters( '404_to_301_settings_import', $incoming, $body );

		if ( empty( $incoming ) ) {
			return new WP_Error(
				'rest_empty_payload',
				__( 'Import payload contained no usable settings.', '404-to-301' ),
				array( 'status' => 400 )
			);
		}

		// Merge over the current settings rather than replacing them
		// outright — this way an envelope from an older plugin version
		// (missing some newer keys) inherits the destination's
		// defaults for everything it doesn't carry, instead of
		// resetting them to null.
		$store   = SettingsStore::instance();
		$current = $store->all();
		$merged  = array_merge( $current, $incoming );

		// Internal keys come from $current via the merge above — they
		// stay pinned to whatever the destination site already had.
		$result = $store->update( $merged );

		if ( ! $result ) {
			return new WP_Error(
				'rest_import_failed',
				__( 'No settings changed — the imported values matched what was already on this site.', '404-to-301' ),
				array( 'status' => 200 )
			);
		}

		return $this->respond(
			array(
				'imported' => count( $incoming ),
				'settings' => $store->all(),
			)
		);
	}
}
