<?php
/**
 * Upgrader class for v4.0.0 settings.
 *
 * From v4 we have different structure for settings.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    Database
 * @subpackage Upgrades\Settings
 */

namespace DuckDev\Redirect\Database\Upgrades;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Settings.
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Database\Upgrades
 */
class Settings {

	/**
	 * Start the upgrades.
	 *
	 * Handle different version upgrades.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $version Existing plugin version.
	 *
	 * @return void
	 */
	public function upgrade( $version ) {
		// V4 upgrade.
		if ( version_compare( $version, '4.0.0-beta', '<' ) ) {
			$this->upgrade_4000();
		}
	}

	/**
	 * Upgrade old plugin settings to new v4 structure.
	 *
	 * Migrate plugin review notice time also.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function upgrade_4000() {
		// Old setting keys.
		$keys = array(
			'i4t3_gnrl_options',
			'i4t3_activated_time',
			'i4t3_db_version',
			'i4t3_version_no',
			'i4t3_review_notice',
		);

		// Should upgrade?
		$upgrade = false;

		// Needs upgrade only if any of the setting exist.
		foreach ( $keys as $key ) {
			if ( get_option( $key ) ) {
				$upgrade = true;
				break;
			}
		}

		if ( $upgrade ) {
			// Get old settings.
			$old = get_option( 'i4t3_gnrl_options' );

			if ( ! empty( $old ) ) {
				$settings = array(
					'disable_guessing'     => ! empty( $old['disable_guessing'] ),
					'monitor_changes'      => true,
					'exclude_paths'        => empty( $old['exclude_paths'] ) ? array() : explode( "\n", $old['exclude_paths'] ),
					'redirect_enabled'     => ! empty( $old['redirect_to'] ),
					'redirect_type'        => $old['redirect_type'],
					'redirect_target'      => 'page' === $old['redirect_to'] ? 'page' : 'link',
					'redirect_link'        => empty( $old['redirect_link'] ) ? home_url() : esc_url_raw( $old['redirect_link'] ),
					'redirect_page'        => empty( $old['redirect_page'] ) ? '' : $old['redirect_page'],
					'logs_enabled'         => ! empty( $old['redirect_log'] ),
					'logs_skip_duplicates' => true,
					'email_enabled'        => ! empty( $old['email_notify'] ),
					'email_recipient'      => empty( $old['email_notify_address'] ) ? get_option( 'admin_email' ) : $old['email_notify_address'],
					'logs_upgraded'        => false,
				);

				// Update the settings.
				dd4t3_settings()->update( $settings );
			}

			// Upgrade review notice time.
			if ( get_option( 'i4t3_review_notice' ) ) {
				update_option(
					'404_to_301_reviews_time',
					get_option( 'i4t3_review_notice' )
				);
			}

			// Delete old settings.
			foreach ( $keys as $key ) {
				delete_option( $key );
			}
		}
	}
}
