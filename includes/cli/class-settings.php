<?php
/**
 * `wp 404-to-301 settings ...` subcommands.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\CLI;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Settings as SettingsModel;
use WP_CLI;

/**
 * Class Settings
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\CLI
 */
class Settings extends Command {

	/**
	 * Register the subcommand.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function register(): void {
		WP_CLI::add_command( '404-to-301 settings', static::class );
	}

	/**
	 * Get one or all settings.
	 *
	 * ## OPTIONS
	 *
	 * [<key>]
	 * : Setting key. Omit to dump every key.
	 *
	 * [--format=<format>]
	 * : table | csv | json | yaml.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function get( array $args, array $assoc ): void {
		$settings = SettingsModel::instance();

		if ( empty( $args ) ) {
			$rows = array();
			foreach ( $settings->all() as $key => $value ) {
				$rows[] = array(
					'key'   => $key,
					'value' => is_scalar( $value ) ? (string) $value : wp_json_encode( $value ),
				);
			}
			$this->print_rows( $assoc, $rows, array( 'key', 'value' ) );
			return;
		}

		$key   = (string) $args[0];
		$value = $settings->get( $key );

		if ( null === $value ) {
			WP_CLI::error( sprintf( __( 'Setting "%s" not found.', '404-to-301' ), $key ) );
		}

		if ( 'json' === ( $assoc['format'] ?? '' ) ) {
			WP_CLI::log( wp_json_encode( $value ) );
		} else {
			WP_CLI::log( is_scalar( $value ) ? (string) $value : wp_json_encode( $value ) );
		}
	}

	/**
	 * Update a single setting.
	 *
	 * ## OPTIONS
	 *
	 * <key>
	 * : Setting key.
	 *
	 * <value>
	 * : New value. JSON-decoded when possible so arrays/objects come
	 *   through correctly; otherwise stored as a string.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function update( array $args, array $assoc ): void {
		$key   = (string) ( $args[0] ?? '' );
		$value = $args[1] ?? '';

		if ( '' === $key ) {
			WP_CLI::error( __( 'Provide a setting key.', '404-to-301' ) );
		}

		$decoded = json_decode( (string) $value, true );
		if ( null !== $decoded || 'null' === $value ) {
			$value = $decoded;
		}

		SettingsModel::instance()->set( $key, $value );

		WP_CLI::success(
			sprintf(
				/* translators: %s: setting key */
				__( 'Updated setting "%s".', '404-to-301' ),
				$key
			)
		);
	}

	/**
	 * Reset every setting to its default.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Skip the confirmation prompt.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args.
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function reset( array $args, array $assoc ): void {
		WP_CLI::confirm( __( 'Reset every setting to its default?', '404-to-301' ), $assoc );

		$settings = SettingsModel::instance();
		$settings->update( $settings->defaults() );

		WP_CLI::success( __( 'Settings reset.', '404-to-301' ) );
	}
}
