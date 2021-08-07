<?php
/**
 * Upgrader class for v4.0.0 settings.
 *
 * From v4 we have different structure for settings.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Database
 * @subpackage Upgrades\V4_Settings
 */

namespace DuckDev\Redirect\Database\Upgrades;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Upgrades\Process;

/**
 * Class V4_Settings.
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Database\Upgrades
 * @extends Process
 */
class V4_Settings extends Process {

	/**
	 * Holds a unique name for the process.
	 *
	 * @var string $id
	 *
	 * @since  4.0.0
	 * @access protected
	 */
	protected $id = 'v4_settings';

	/**
	 * Old settings keys to process.
	 *
	 * @var string[] $keys
	 *
	 * @since  4.0.0
	 * @access private
	 */
	private $keys = array(
		'i4t3_gnrl_options',
		'i4t3_activated_time',
		'i4t3_db_version',
		'i4t3_version_no',
		'i4t3_review_notice',
	);

	/**
	 * Check if we can continue with upgrade.
	 *
	 * Continue only if old settings exist.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function should_upgrade() {
		return $this->old_settings_exists();
	}

	/**
	 * Get the data to upgrade.
	 *
	 * Simply return a fake array because we will get
	 * the settings data during the upgrade.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return array|false
	 */
	public function get_data() {
		return $this->old_settings_exists() ? array( 'settings' ) : false;
	}

	/**
	 * Upgrade the settings data.
	 *
	 * @param mixed $item Data to upgrade.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade_task( $item ) {
		// Upgrade old settings.
		$this->upgrade_settings();
		// Upgrade other settings data.
		$this->upgrade_others();
		// Now delete the settings.
		$this->delete_old_settings();
	}

	/**
	 * Upgrade old plugin settings to new structure.
	 *
	 * It will continue only if old settings exist in db.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function upgrade_settings() {
		// Get old settings.
		$old = get_option( 'i4t3_gnrl_options' );

		if ( ! empty( $old ) ) {
			$settings = array(
				'general'  => array(
					'disable_guess'   => ! empty( $old['disable_guessing'] ),
					'monitor_changes' => true,
					'exclude'         => empty( $old['exclude_paths'] ) ? array() : explode( "\n", $old['exclude_paths'] ),
				),
				'redirect' => array(
					'enable' => ! empty( $old['redirect_to'] ),
					'type'   => $old['redirect_type'],
					'target' => 'page' === $old['redirect_to'] ? 'page' : 'link',
					'link'   => empty( $old['redirect_link'] ) ? home_url() : esc_url_raw( $old['redirect_link'] ),
					'page'   => empty( $old['redirect_page'] ) ? '' : esc_url_raw( $old['redirect_page'] ),
				),
				'logs'     => array(
					'enable'          => ! empty( $old['redirect_log'] ),
					'skip_duplicates' => true,
				),
				'email'    => array(
					'enable'    => ! empty( $old['email_notify'] ),
					'recipient' => empty( $old['email_notify_address'] ) ? get_option( 'admin_email' ) : $old['email_notify_address'],
				),
			);

			// Update the settings.
			dd4t3_settings()->update_settings( $settings );
		}
	}

	/**
	 * Upgrade other options from old version.
	 *
	 * Only review notice time flag is required and upgrade.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function upgrade_others() {
		// Upgrade review notice time.
		if ( get_option( 'i4t3_review_notice' ) ) {
			update_site_option(
				'404_to_301_reviews_time',
				get_option( 'i4t3_review_notice' )
			);
		}
	}

	/**
	 * Check if old setting exist.
	 *
	 * Return true if any of the options are available.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool
	 */
	private function old_settings_exists() {
		foreach ( $this->keys as $key ) {
			if ( get_option( $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Delete old settings from db.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	private function delete_old_settings() {
		foreach ( $this->keys as $key ) {
			delete_option( $key );
		}
	}
}
