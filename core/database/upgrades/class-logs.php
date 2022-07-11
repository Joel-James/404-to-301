<?php
/**
 * The v4 logs upgrade process class.
 *
 * Upgrading old error logs to new tables using background processing.
 *
 * @since      4.0.0
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @package    Database
 * @subpackage Upgrades\Logs
 */

namespace DuckDev\Redirect\Database\Upgrades;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Plugin;
use DuckDev\Redirect\Views\View;

/**
 * Class Logs.
 *
 * @since   4.0.0
 * @package DuckDev\Redirect\Database\Upgrades
 */
class Logs {

	/**
	 * Upgrade in progress flag.
	 *
	 * @since 4.0.0
	 * @var bool $upgrading
	 */
	private $upgrading = false;

	/**
	 * Initialize the upgrade class.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		// Process the upgrade action.
		add_action( 'dd4t3_logs_upgrade', array( $this, 'upgrade' ) );

		// Show admin notice for upgrade.
		add_action( 'dd4t3_admin_notices', array( $this, 'upgrade_notice' ) );
	}

	/**
	 * Show the upgrade logs notice for admins.
	 *
	 * If old logs table exist and upgrade has not started, show the
	 * upgrade start notice. If upgrade is already in progress, show
	 * the in progress notice.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function upgrade_notice() {
		if ( $this->old_table_exists() ) {
			View::instance()->render(
				'components/notices/upgrade-notice',
				array(
					'plugin'              => Plugin::name(),
					'upgrading'           => $this->is_upgrading(),
					'scheduler_available' => $this->is_scheduler_ready(),
				)
			);
		}
	}

	/**
	 * Start the upgrader process.
	 *
	 * This should be called once. Once started,
	 * upgrader will continue by it's own if required.
	 *
	 * @since 4.0.0
	 *
	 * @param string $action Action name (skip, upgrade_redirects, upgrade_all).
	 *
	 * @return void
	 */
	public function start( $action = 'upgrade_redirects' ) {
		if ( 'skip' === $action ) {
			// Delete old table.
			$this->drop_old_table();
		} elseif ( $this->should_upgrade() ) {
			// If only custom redirects required, cleanup others.
			if ( 'upgrade_redirects' === $action ) {
				$this->clean_normal_logs();
			}

			// Immediately start upgrade.
			$this->schedule_next( $action );
		}
	}

	/**
	 * Run the upgrade process for logs.
	 *
	 * Take one log from the old table and upgrade it.
	 * After the upgrade delete it from the old log.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @param string $action Action name.
	 *
	 * @return bool
	 */
	public function upgrade( $action ) {
		// Old table doesn't exist.
		if ( ! $this->old_table_exists() ) {
			return $this->complete();
		}

		// Get next log for upgrade.
		$log = $this->get_next_log();

		// No logs left.
		if ( empty( $log['id'] ) ) {
			// Make sure to drop table.
			$this->drop_old_table();

			return $this->complete();
		}

		// Get options.
		$options = $this->get_value( 'options', $log, array() );
		$options = empty( $options ) ? array() : maybe_unserialize( $options );

		// Get redirect status.
		$redirect_status = $this->get_status( $this->get_value( 'redirect', $options, 2 ) );

		// Create redirect if required.
		$redirect_id = $this->create_redirect( $log, $options, $redirect_status );

		// Create new log.
		$this->create_log( $log, $options, $redirect_id, $redirect_status );

		// Delete log.
		$this->delete_old_log( $log['id'] );

		// Immediately start upgrade.
		$this->schedule_next( $action );
	}

	/**
	 * Create new log item from old data.
	 *
	 * Few field names has been changed and new fields are added.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @param array    $log             Log data.
	 * @param array    $options         Old options.
	 * @param int|bool $redirect_id     Redirect ID.
	 * @param string   $redirect_status Redirect status.
	 *
	 * @return void
	 */
	private function create_log( $log, array $options, $redirect_id = false, $redirect_status = 'enabled' ) {
		global $wpdb;

		// Get the url.
		$url = $this->get_value( 'url', $log, '' );

		// URL is required.
		if ( ! empty( $url ) ) {
			// Get already added URLs.
			$urls = dd4t3_cache()->get_transient( 'upgraded_log_urls' );
			if ( empty( $urls ) ) {
				$urls = array();
			}

			$table = $this->table_name( '404_to_301_logs' );
			$url   = esc_url_raw( $url );

			if ( isset( $urls[ $url ] ) ) {
				// Update the count.
				$urls[ $url ] = $urls[ $url ] + 1;
				// Update the count in db.
				$wpdb->query( $wpdb->prepare( 'UPDATE %1$s SET hits = "%2$d" WHERE url = "%3$s"', $table, intval( $urls[ $url ] ), $url ) );
			} else {
				$success = $wpdb->insert(
					$table,
					array(
						'url'             => $url,
						'referrer'        => esc_url_raw( $this->get_value( 'ref', $log ) ),
						'ip'              => sanitize_text_field( $this->get_value( 'ip', $log ) ),
						'agent'           => sanitize_text_field( $this->get_value( 'ua', $log ) ),
						'request_method'  => 'GET',
						'hits'            => 1,
						'redirect_status' => esc_attr( $redirect_status ),
						'log_status'      => $this->get_status( $this->get_value( 'log', $options, 2 ) ),
						'email_status'    => $this->get_status( $this->get_value( 'alert', $options, 2 ) ),
						'redirect_id'     => empty( $redirect_id ) ? null : intval( $redirect_id ),
						'created_at'      => $this->get_value( 'date', $log, current_time( 'mysql' ) ),
						'updated_at'      => current_time( 'mysql' ),
						'updated_by'      => function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0,
					)
				);

				// Add URL to the list.
				if ( ! empty( $success ) ) {
					$urls[ $url ] = 1;
				}
			}

			// Set the updated list to transient.
			dd4t3_cache()->set_transient( 'upgraded_log_urls', $urls );
		}
	}

	/**
	 * Create new custom redirect from old data.
	 *
	 * If a custom redirect link is found, create an item
	 * for our redirects table.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @param array  $log             Log item.
	 * @param array  $options         Old options.
	 * @param string $redirect_status Redirect status.
	 *
	 * @return int|false
	 */
	private function create_redirect( $log, array $options, $redirect_status = 'enabled' ) {
		global $wpdb;

		// Redirect from.
		$source = sanitize_text_field( $this->get_value( 'url', $log ) );
		// Redirect to.
		$destination = $this->get_value( 'redirect', $log );

		// Only if source and destination can be set.
		if ( ! empty( $source ) && ! empty( $destination ) ) {
			// Get already added URLs.
			$urls = dd4t3_cache()->get_transient( 'upgraded_redirect_urls' );
			if ( empty( $urls ) ) {
				$urls = array();
			}

			if ( empty( $options['type'] ) ) {
				$code = dd4t3_settings()->get( 'redirect_type', 301 );
			} else {
				$code = $options['type'];
			}

			if ( ! isset( $urls[ $source ] ) ) {
				// Insert new redirect.
				$success = $wpdb->insert(
					$this->table_name( '404_to_301_redirects' ),
					array(
						'source'      => $source,
						'destination' => esc_url_raw( $destination ),
						'type'        => (int) $code,
						'status'      => esc_attr( $redirect_status ),
						'group'       => '404',
						'hash'        => md5( $source ),
						'created_at'  => $this->get_value( 'date', $log, current_time( 'mysql' ) ), // Not correct, still.
					)
				);

				// Add redirect id to the already added list.
				if ( $success ) {
					$urls[ $source ] = $wpdb->insert_id;

					// Set the updated list to transient.
					dd4t3_cache()->set_transient( 'upgraded_redirect_urls', $urls );
				}
			}

			if ( isset( $urls[ $source ] ) ) {
				// Return redirect id.
				return $urls[ $source ];
			}
		}

		return false;
	}

	/**
	 * Get next log for upgrade from old table.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array|bool
	 */
	private function get_next_log() {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		$log = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table ORDER BY id DESC LIMIT 1" ), ARRAY_A );

		return empty( $log ) ? false : $log;
	}

	/**
	 * Clean old logs table to remove all logs except customized ones.
	 *
	 * Do this only when required to upgrade custom redirects.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return void
	 */
	private function clean_normal_logs() {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		$wpdb->query( "DELETE FROM $table WHERE options IS NULL AND redirect = ''" );
	}

	/**
	 * Delete the upgraded log entry from old table.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @param int $id Log ID.
	 *
	 * @return void
	 */
	private function delete_old_log( $id ) {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		$wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE id = %d", intval( $id ) ) );
	}

	/**
	 * Clean up the action scheduler log and other entries to not
	 * flood the db with a lot of data.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function clean_data() {
		global $wpdb;
		$id = $wpdb->get_var( 'SELECT action_id FROM wp_actionscheduler_actions WHERE hook = \'dd4t3_logs_upgrade\' ORDER BY action_id DESC LIMIT 1' );
		if ( ! empty( $id ) ) {
			$wpdb->query( 'DELETE as_actions, as_logs FROM wp_actionscheduler_actions as_actions JOIN wp_actionscheduler_logs as_logs ON as_logs.action_id = as_actions.action_id WHERE as_actions.hook = \'dd4t3_logs_upgrade\' AND as_actions.action_id != ' . $id );
		}
	}

	/**
	 * Clean up the action scheduler log and other entries to not
	 * flood the db with a lot of data.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function clean_scheduler_data() {
		global $wpdb;

		$wpdb->query( 'DELETE as_actions, as_logs FROM wp_actionscheduler_actions as_actions JOIN wp_actionscheduler_logs as_logs ON as_logs.action_id = as_actions.action_id WHERE as_actions.hook = \'dd4t3_logs_upgrade\'' );
	}

	/**
	 * Schedule next upgrade action.
	 *
	 * Use this to start or continue upgrade process.
	 *
	 * @since 4.0.0
	 *
	 * @param string $action Action name.
	 *
	 * @return void
	 */
	private function schedule_next( $action ) {
		// Immediately start upgrade.
		as_enqueue_async_action(
			'dd4t3_logs_upgrade',
			array( 'action' => $action ),
			'404-to-301'
		);
	}

	/**
	 * Mark the upgrade process as completed.
	 *
	 * Unschedule all actions (just in case).
	 * Delete the logs table if exist.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function complete() {
		// Make sure to cleanup.
		as_unschedule_all_actions( 'dd4t3_logs_upgrade' );

		// Clean all action scheduler logs.
		$this->clean_scheduler_data();

		return true;
	}

	/**
	 * Check if log upgrade is required.
	 *
	 * Log upgrade is required only when old log table exist
	 * and action scheduler is available.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function should_upgrade() {
		return $this->old_table_exists() && $this->is_scheduler_ready();
	}

	/**
	 * Check if log upgrade action is scheduled.
	 *
	 * If upgrading is in progress there will be a queue process
	 * scheduled or running.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function is_upgrading() {
		// Check if upgrade action is scheduled.
		if ( function_exists( 'as_has_scheduled_action' ) ) {
			return as_has_scheduled_action( 'dd4t3_logs_upgrade' );
		}

		// Or check if upgrading flag is set.
		return $this->upgrading;
	}

	/**
	 * Check if Action Scheduler is available for use.
	 *
	 * Action scheduler can be installed as a plugin or as a library
	 * included in an active plugin.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function is_scheduler_ready() {
		// Class not found.
		if ( ! class_exists( '\ActionScheduler_Versions' ) ) {
			return false;
		}

		// If not required method exist.
		if ( ! method_exists( \ActionScheduler_Versions::instance(), 'latest_version' ) ) {
			return false;
		}

		// We need action scheduler v3.0 or above.
		return version_compare( \ActionScheduler_Versions::instance()->latest_version(), '3.0', '>=' );
	}

	/**
	 * Get the plugin install URL for action scheduler.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function scheduler_install_url() {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'action-scheduler',
				),
				admin_url( 'update.php' )
			)
		);
	}

	/**
	 * Check if old logs table exist.
	 *
	 * We need to run upgrade only if the table exist.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return bool
	 */
	private function old_table_exists() {
		static $exists = null;

		if ( null === $exists ) {
			global $wpdb;
			// Table name.
			$table = $this->table_name( '404_to_301' );

			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );

			$exists = $wpdb->get_var( $query ) === $table;
		}

		return $exists;
	}

	/**
	 * Remove the old logs table completely.
	 *
	 * This can not be undone. Please be careful.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function drop_old_table() {
		global $wpdb;

		$table = $this->table_name( '404_to_301' );

		$wpdb->query( "DROP TABLE IF EXISTS $table" );
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @param string $table Table key.
	 *
	 * @return string
	 */
	private function table_name( $table ) {
		global $wpdb;

		return $wpdb->prefix . $table;
	}

	/**
	 * Get an item from data array.
	 *
	 * If not found, return default value.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @param string $key     Item key.
	 * @param array  $data    Data to check.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	private function get_value( $key, $data, $default = '' ) {
		return isset( $data[ $key ] ) ? $data[ $key ] : $default;
	}

	/**
	 * Helper method to get a new status value from old one.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @param mixed $value Value to check.
	 *
	 * @return string
	 */
	private function get_status( $value ) {
		$value = (int) $value;
		switch ( $value ) {
			case 0:
				return 'disabled';
			case 1:
				return 'enabled';
			default:
				return 'global';
		}
	}
}
