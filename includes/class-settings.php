<?php
/**
 * Plugin settings.
 *
 * Single option (`404_to_301_settings`) that holds every plugin
 * setting as a structured object. Registered with the REST API via
 * `show_in_rest`, so the React UI reads/writes it through the
 * `/wp/v2/settings` endpoint the same way LLC does.
 *
 * The class also owns the one-shot legacy migration: when the v3
 * option `i4t3_gnrl_options` is the only data on disk, we map it to
 * the v4 schema once on activation and then forget it lived.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Utils\Sanitizer;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Settings
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
class Settings extends Singleton {

	/**
	 * Option key used to store the settings object.
	 *
	 * @since 4.0.0
	 */
	const KEY = '404_to_301_settings';

	/**
	 * Legacy v3 option key. Read once on activation, mapped into the
	 * new option, then ignored on every subsequent boot.
	 *
	 * @since 4.0.0
	 */
	const LEGACY_KEY = 'i4t3_gnrl_options';

	/**
	 * Initialise the settings.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Get the default settings.
	 *
	 * Filterable so add-ons can register their own keys.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function defaults(): array {
		/**
		 * Filter the default plugin settings.
		 *
		 * @since 4.0.0
		 *
		 * @param array $defaults Default settings.
		 */
		return (array) apply_filters(
			'404_to_301_settings_defaults',
			array(
				// General.
				'disable_guessing'     => true,
				'exclude_paths'        => array(),
				'monitor_post_slug'    => false,
				'mask_ip'              => false,
				'track_admin_404'      => false,

				// Redirects (global defaults — per-row settings live on the redirects table).
				'redirect_enabled'     => true,
				'redirect_type'        => '301',
				'redirect_target'      => 'link',
				'redirect_link'        => home_url(),
				'redirect_page'        => 0,

				// Logs.
				'logs_enabled'         => true,
				'logs_skip_bots'       => true,
				'logs_skip_duplicates' => false,

				// Notifications.
				'email_enabled'        => false,
				'email_recipient'      => (string) get_option( 'admin_email' ),
				'email_threshold'      => 1,

				// Internal — not exposed in the settings UI.
				'plugin_version'       => '',
				'db_version'           => '',
				'logs_migrated'        => false,
				'phase1_done'          => false,
				'legacy_table_dropped' => false,
			)
		);
	}

	/**
	 * Get every setting, falling back to defaults for missing keys.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function all(): array {
		$defaults = $this->defaults();
		$stored   = get_option( self::KEY, array() );
		$stored   = is_array( $stored ) ? $stored : array();

		return wp_parse_args( $stored, $defaults );
	}

	/**
	 * Get a single setting value.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Value returned when the key does not exist.
	 *
	 * @return mixed
	 */
	public function get( string $key, $fallback = null ) {
		$settings = $this->all();

		return array_key_exists( $key, $settings ) ? $settings[ $key ] : $fallback;
	}

	/**
	 * Update a single setting value.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 *
	 * @return bool
	 */
	public function set( string $key, $value ): bool {
		$settings         = $this->all();
		$settings[ $key ] = $value;

		return $this->update( $settings );
	}

	/**
	 * Update the entire settings object.
	 *
	 * @since 4.0.0
	 *
	 * @param array $values Settings values.
	 *
	 * @return bool
	 */
	public function update( array $values ): bool {
		return update_option( self::KEY, $this->sanitize( $values ) );
	}

	/**
	 * Register the option with WordPress + the REST API.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		register_setting(
			'options',
			self::KEY,
			array(
				'type'              => 'object',
				'description'       => __( '404 to 301 plugin settings.', '404-to-301' ),
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => $this->defaults(),
				'show_in_rest'      => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => $this->rest_schema_properties(),
					),
				),
			)
		);
	}

	/**
	 * Sanitise the settings before save.
	 *
	 * Each field is routed through the matching {@see Sanitizer}
	 * method so the option write path and the REST schema agree on
	 * what a clean value looks like.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $values Raw values.
	 *
	 * @return array
	 */
	public function sanitize( $values ): array {
		$values   = is_array( $values ) ? $values : array();
		$current  = $this->all();
		$defaults = $this->defaults();
		$clean    = array();

		foreach ( $defaults as $key => $default ) {
			// Keep the current value for any key not in this request.
			$raw = array_key_exists( $key, $values ) ? $values[ $key ] : $current[ $key ];

			switch ( $key ) {
				case 'disable_guessing':
				case 'monitor_post_slug':
				case 'mask_ip':
				case 'track_admin_404':
				case 'redirect_enabled':
				case 'logs_enabled':
				case 'logs_skip_bots':
				case 'logs_skip_duplicates':
				case 'email_enabled':
				case 'logs_migrated':
				case 'phase1_done':
				case 'legacy_table_dropped':
					$clean[ $key ] = Sanitizer::boolean( $raw );
					break;

				case 'redirect_type':
					$clean[ $key ] = Sanitizer::enum( $raw, array( '301', '302', '307' ), '301' );
					break;

				case 'redirect_target':
					$clean[ $key ] = Sanitizer::enum( $raw, array( 'link', 'page', 'none' ), 'link' );
					break;

				case 'redirect_link':
					$clean[ $key ] = Sanitizer::url( $raw );
					break;

				case 'redirect_page':
					$clean[ $key ] = Sanitizer::integer( $raw, 0 );
					break;

				case 'email_threshold':
					$clean[ $key ] = Sanitizer::integer( $raw, 1 );
					break;

				case 'email_recipient':
					$clean[ $key ] = Sanitizer::email( $raw );
					break;

				case 'exclude_paths':
					$clean[ $key ] = Sanitizer::string_list( $raw );
					break;

				case 'plugin_version':
				case 'db_version':
					$clean[ $key ] = Sanitizer::text( $raw );
					break;

				default:
					$clean[ $key ] = $raw;
					break;
			}
		}

		/**
		 * Filter the sanitised settings before they are written to disk.
		 *
		 * @since 4.0.0
		 *
		 * @param array $clean    Sanitised values about to be saved.
		 * @param array $values   Raw values from the request.
		 * @param array $current  Previous settings.
		 */
		return (array) apply_filters( '404_to_301_settings_pre_update', $clean, $values, $current );
	}

	/**
	 * REST schema describing every settings property.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	private function rest_schema_properties(): array {
		/**
		 * Filter the REST schema property map.
		 *
		 * Addons that introduce their own keys via the
		 * `404_to_301_settings_defaults` filter should also hook in
		 * here so their keys are accepted by the `/wp/v2/settings`
		 * endpoint instead of being silently dropped on save.
		 *
		 * @since 4.0.0
		 *
		 * @param array $properties Property map keyed by setting name.
		 */
		return (array) apply_filters(
			'404_to_301_settings_rest_schema',
			array(
				'disable_guessing'     => array( 'type' => 'boolean' ),
				'exclude_paths'        => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'monitor_post_slug'    => array( 'type' => 'boolean' ),
				'mask_ip'              => array( 'type' => 'boolean' ),
				'track_admin_404'      => array( 'type' => 'boolean' ),

				'redirect_enabled'     => array( 'type' => 'boolean' ),
				'redirect_type'        => array(
					'type' => 'string',
					'enum' => array( '301', '302', '307' ),
				),
				'redirect_target'      => array(
					'type' => 'string',
					'enum' => array( 'link', 'page', 'none' ),
				),
				'redirect_link'        => array(
					'type'   => 'string',
					'format' => 'uri',
				),
				'redirect_page'        => array( 'type' => 'integer' ),

				'logs_enabled'         => array( 'type' => 'boolean' ),
				'logs_skip_bots'       => array( 'type' => 'boolean' ),
				'logs_skip_duplicates' => array( 'type' => 'boolean' ),

				'email_enabled'        => array( 'type' => 'boolean' ),
				'email_recipient'      => array(
					'type'   => 'string',
					'format' => 'email',
				),
				'email_threshold'      => array( 'type' => 'integer' ),

				'plugin_version'       => array( 'type' => 'string' ),
				'db_version'           => array( 'type' => 'string' ),
				'logs_migrated'        => array( 'type' => 'boolean' ),
				'phase1_done'          => array( 'type' => 'boolean' ),
				'legacy_table_dropped' => array( 'type' => 'boolean' ),
			)
		);
	}

	/**
	 * Map the legacy v3 option onto the v4 schema, exactly once.
	 *
	 * Called from {@see \DuckDev\FourNotFour\Setup\Activator::run()}.
	 * Runs only when the v4 option doesn't exist yet — every later
	 * boot is a no-op.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function maybe_migrate_legacy(): void {
		// The v4 option already exists — nothing to do.
		if ( false !== get_option( self::KEY, false ) ) {
			return;
		}

		$settings = $this->defaults();
		$legacy   = get_option( self::LEGACY_KEY, null );

		if ( is_array( $legacy ) ) {
			// Direct one-to-one fields.
			if ( isset( $legacy['redirect_type'] ) ) {
				$settings['redirect_type'] = Sanitizer::enum(
					(string) $legacy['redirect_type'],
					array( '301', '302', '307' ),
					'301'
				);
			}

			if ( isset( $legacy['redirect_to'] ) ) {
				// v3 used 'page', 'link' or '0' (off). Map '0' to 'none'.
				$map                          = array(
					'page' => 'page',
					'link' => 'link',
					'0'    => 'none',
				);
				$key                          = (string) $legacy['redirect_to'];
				$settings['redirect_target']  = $map[ $key ] ?? 'link';
				$settings['redirect_enabled'] = 'none' !== $settings['redirect_target'];
			}

			if ( isset( $legacy['redirect_link'] ) ) {
				$settings['redirect_link'] = Sanitizer::url( (string) $legacy['redirect_link'] );
			}

			if ( isset( $legacy['redirect_page'] ) ) {
				$settings['redirect_page'] = Sanitizer::integer( $legacy['redirect_page'], 0 );
			}

			if ( isset( $legacy['redirect_log'] ) ) {
				$settings['logs_enabled'] = Sanitizer::boolean( $legacy['redirect_log'] );
			}

			if ( isset( $legacy['email_notify'] ) ) {
				$settings['email_enabled'] = Sanitizer::boolean( $legacy['email_notify'] );
			}

			if ( isset( $legacy['email_notify_address'] ) ) {
				$settings['email_recipient'] = Sanitizer::email( (string) $legacy['email_notify_address'] );
			}

			if ( isset( $legacy['disable_guessing'] ) ) {
				$settings['disable_guessing'] = Sanitizer::boolean( $legacy['disable_guessing'] );
			}

			if ( isset( $legacy['exclude_paths'] ) ) {
				$settings['exclude_paths'] = Sanitizer::string_list( $legacy['exclude_paths'] );
			}
		}

		$settings['plugin_version'] = D404_VERSION;
		$settings['db_version']     = D404_DB_VERSION;

		update_option( self::KEY, $settings );
	}
}
