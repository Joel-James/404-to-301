<?php
/**
 * WordPress Site Health integration.
 *
 * Surfaces 404-to-301 diagnostics in Tools → Site Health: a
 * dedicated section in the Info tab (read-only state dump) and a
 * handful of Status tests that flag the issues we've seen account
 * for most support tickets — log table bloat, conflicting redirect
 * plugins, broken default targets, silent cron failures on the
 * Email Reports and Logs Cleaner addons.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Core;
use DuckDev\FourNotFour\Plugin;
use DuckDev\FourNotFour\Utils\Singleton;

/**
 * Class Site_Health
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Admin
 */
class Site_Health extends Singleton {

	/**
	 * Row-count threshold above which the logs table is considered
	 * "large enough to recommend pruning".
	 *
	 * @since 4.0.0
	 */
	const LOGS_LARGE_ROWS = 50_000;

	/**
	 * Cron-overdue grace multiplier.
	 *
	 * A scheduled hook is flagged as stalled once it is more than
	 * `2 ×` its interval past due — one missed tick is normal noise
	 * (server load, DISABLE_WP_CRON gaps); two missed ticks in a row
	 * is a real problem worth surfacing.
	 *
	 * @since 4.0.0
	 */
	const CRON_OVERDUE_MULTIPLIER = 2;

	/**
	 * Logs Cleaner addon plugin basename.
	 *
	 * Hard-coded rather than read from the addon (the addon may not
	 * be loaded). Matches the basename WordPress uses when the addon
	 * is installed under its canonical directory name.
	 *
	 * @since 4.0.0
	 */
	const CLEANER_BASENAME = '404-to-301-logs-cleaner/404-to-301-logs-cleaner.php';

	/**
	 * Logs Cleaner cron hook name.
	 *
	 * Mirrors the constant the addon defines — duplicated here so we
	 * can probe `wp_next_scheduled()` without requiring the addon's
	 * code to be loaded.
	 *
	 * @since 4.0.0
	 */
	const CLEANER_CRON = 'd404_logs_cleaner_tick';

	/**
	 * Email Reports cron hook name.
	 *
	 * @since 4.0.0
	 */
	const EMAIL_REPORTS_CRON = 'd404_email_reports_tick';

	/**
	 * Register the Site Health filters.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_filter( 'debug_information', array( $this, 'debug_information' ) );
		add_filter( 'site_status_tests', array( $this, 'register_tests' ) );
	}

	// --------------------------------------------------------------------- //
	// Debug Info — Tools → Site Health → Info tab.
	// --------------------------------------------------------------------- //

	/**
	 * Add a "404 to 301" section to the Site Health Info screen.
	 *
	 * @since 4.0.0
	 *
	 * @param array $info Existing debug info sections.
	 *
	 * @return array
	 */
	public function debug_information( $info ): array {
		$info = is_array( $info ) ? $info : array();

		$settings = Core::instance()->settings();
		$values   = $settings ? $settings->all() : array();

		$fields = array(
			'plugin_version'   => array(
				'label' => __( 'Plugin version', '404-to-301' ),
				'value' => defined( 'D404_VERSION' ) ? D404_VERSION : '',
			),
			'db_version'       => array(
				'label' => __( 'Database schema version', '404-to-301' ),
				'value' => defined( 'D404_DB_VERSION' ) ? D404_DB_VERSION : '',
			),
			'redirect_enabled' => array(
				'label' => __( 'Redirect 404s', '404-to-301' ),
				'value' => $this->yes_no( ! empty( $values['redirect_enabled'] ) ),
			),
			'redirect_type'    => array(
				'label' => __( 'Default redirect type', '404-to-301' ),
				'value' => (string) ( $values['redirect_type'] ?? '' ),
			),
			'redirect_target'  => array(
				'label' => __( 'Default redirect target', '404-to-301' ),
				'value' => (string) ( $values['redirect_target'] ?? '' ),
			),
			'redirect_link'    => array(
				'label'   => __( 'Default redirect URL', '404-to-301' ),
				'value'   => (string) ( $values['redirect_link'] ?? '' ),
				'private' => true,
			),
			'disable_guessing' => array(
				'label' => __( 'Disable URL guessing', '404-to-301' ),
				'value' => (string) ( $values['disable_guessing'] ?? '' ),
			),
			'logs_enabled'     => array(
				'label' => __( 'Log 404 errors', '404-to-301' ),
				'value' => $this->yes_no( ! empty( $values['logs_enabled'] ) ),
			),
			'logs_skip_bots'   => array(
				'label' => __( 'Skip bot 404s', '404-to-301' ),
				'value' => $this->yes_no( ! empty( $values['logs_skip_bots'] ) ),
			),
			'logs_rows'        => array(
				'label' => __( 'Logged 404 rows', '404-to-301' ),
				'value' => number_format_i18n( (float) $this->row_count( '404_to_301_logs' ) ),
			),
			'logs_size'        => array(
				'label' => __( 'Logs table size', '404-to-301' ),
				'value' => size_format( $this->table_size_bytes( '404_to_301_logs' ) ),
			),
			'redirect_rows'    => array(
				'label' => __( 'Redirect rules', '404-to-301' ),
				'value' => number_format_i18n( (float) $this->row_count( '404_to_301_redirects' ) ),
			),
			'email_enabled'    => array(
				'label' => __( 'Email notifications', '404-to-301' ),
				'value' => $this->yes_no( ! empty( $values['email_enabled'] ) ),
			),
			'email_recipients' => array(
				'label' => __( 'Email recipients', '404-to-301' ),
				'value' => (string) count( (array) ( $values['email_recipient'] ?? array() ) ),
			),
			'track_admin_404'  => array(
				'label' => __( 'Track admin 404s', '404-to-301' ),
				'value' => $this->yes_no( ! empty( $values['track_admin_404'] ) ),
			),
			'mask_ip'          => array(
				'label' => __( 'Mask logged IPs', '404-to-301' ),
				'value' => $this->yes_no( ! empty( $values['mask_ip'] ) ),
			),
			'cleaner_active'   => array(
				'label' => __( 'Logs Cleaner addon', '404-to-301' ),
				'value' => $this->yes_no( $this->is_cleaner_active() ),
			),
			'cleaner_next_run' => array(
				'label' => __( 'Logs Cleaner next run', '404-to-301' ),
				'value' => $this->format_next_run( self::CLEANER_CRON ),
			),
			'reports_next_run' => array(
				'label' => __( 'Email Reports next run', '404-to-301' ),
				'value' => $this->format_next_run( self::EMAIL_REPORTS_CRON ),
			),
			'conflicting'      => array(
				'label' => __( 'Conflicting redirect plugins', '404-to-301' ),
				'value' => $this->conflicting_plugins_label(),
			),
		);

		$info['404-to-301'] = array(
			'label'       => __( '404 to 301', '404-to-301' ),
			'description' => __( 'Diagnostic information for the 404 to 301 plugin.', '404-to-301' ),
			'fields'      => $fields,
		);

		return $info;
	}

	// --------------------------------------------------------------------- //
	// Status tests — Tools → Site Health → Status tab.
	// --------------------------------------------------------------------- //

	/**
	 * Register our direct status tests.
	 *
	 * Every test is `direct` (synchronous): each callback runs a
	 * single cheap query or option read — no HTTP, no heavy joins —
	 * so adding them to the Status page does not slow it down.
	 *
	 * @since 4.0.0
	 *
	 * @param array $tests Existing site status tests.
	 *
	 * @return array
	 */
	public function register_tests( $tests ): array {
		$tests = is_array( $tests ) ? $tests : array();

		$ours = array(
			'd404_logs_table_size'    => array( __( '404 to 301 logs table size', '404-to-301' ), 'test_logs_table_size' ),
			'd404_cleaner_cron'       => array( __( '404 to 301 Logs Cleaner cron', '404-to-301' ), 'test_cleaner_cron' ),
			'd404_email_reports_cron' => array( __( '404 to 301 Email Reports cron', '404-to-301' ), 'test_email_reports_cron' ),
			'd404_conflicting_plugin' => array( __( '404 to 301 conflicting plugins', '404-to-301' ), 'test_conflicting_plugins' ),
			'd404_logging_state'      => array( __( '404 to 301 logging state', '404-to-301' ), 'test_logging_state' ),
			'd404_db_version'         => array( __( '404 to 301 database schema', '404-to-301' ), 'test_db_version' ),
		);

		foreach ( $ours as $id => list( $label, $method ) ) {
			$tests['direct'][ $id ] = array(
				'label' => $label,
				'test'  => array( $this, $method ),
			);
		}

		return $tests;
	}

	/**
	 * Test: is the logs table large enough that auto-cleanup is worth
	 * recommending?
	 *
	 * Branches on whether the Logs Cleaner addon (premium) is active:
	 * when it is, we point at its settings; when it isn't, we point at
	 * the Addons page so the user can install it.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function test_logs_table_size(): array {
		$rows = $this->row_count( '404_to_301_logs' );

		if ( $rows < self::LOGS_LARGE_ROWS ) {
			return $this->build_result(
				'good',
				'd404_logs_table_size',
				__( 'Your 404 logs table is a healthy size', '404-to-301' ),
				__( 'The 404 to 301 logs table is well within recommended limits.', '404-to-301' )
			);
		}

		$description = sprintf(
			/* translators: %s: number of rows in the logs table. */
			__( 'Your 404 to 301 logs table currently holds %s rows. Large log tables slow down queries on the Logs screen and bloat your database backups.', '404-to-301' ),
			number_format_i18n( (float) $rows )
		);

		if ( $this->is_cleaner_active() ) {
			$description .= ' ' . __( 'The Logs Cleaner addon is active — set a cleanup policy to keep the table trimmed automatically.', '404-to-301' );
			$action_label = __( 'Configure Auto Cleanup', '404-to-301' );
			$action_url   = Plugin::get_url( 'settings' );
		} else {
			$description .= ' ' . __( 'The Logs Cleaner addon can prune old entries automatically on a schedule you choose.', '404-to-301' );
			$action_label = __( 'Get the Logs Cleaner addon', '404-to-301' );
			$action_url   = Plugin::get_url( 'addons' );
		}

		return $this->build_result(
			'recommended',
			'd404_logs_table_size',
			__( 'Your 404 logs table is getting large', '404-to-301' ),
			$description,
			$action_label,
			$action_url
		);
	}

	/**
	 * Test: if the Logs Cleaner addon is active, is its cron firing on
	 * schedule? Skipped entirely when the addon is not installed.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function test_cleaner_cron(): array {
		if ( ! $this->is_cleaner_active() ) {
			return $this->build_result(
				'good',
				'd404_cleaner_cron',
				__( 'Logs Cleaner is not installed', '404-to-301' ),
				__( 'No cron health check needed — the Logs Cleaner addon is not active.', '404-to-301' )
			);
		}

		return $this->cron_health_result(
			'd404_cleaner_cron',
			self::CLEANER_CRON,
			__( 'Logs Cleaner', '404-to-301' ),
			Plugin::get_url( 'settings' )
		);
	}

	/**
	 * Test: if Email Reports cron is scheduled, is it on schedule?
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function test_email_reports_cron(): array {
		if ( ! wp_next_scheduled( self::EMAIL_REPORTS_CRON ) ) {
			return $this->build_result(
				'good',
				'd404_email_reports_cron',
				__( 'Email Reports cron is not scheduled', '404-to-301' ),
				__( 'No cron health check needed — the Email Reports addon is not active or not configured to send reports.', '404-to-301' )
			);
		}

		return $this->cron_health_result(
			'd404_email_reports_cron',
			self::EMAIL_REPORTS_CRON,
			__( 'Email Reports', '404-to-301' ),
			Plugin::get_url( 'settings' )
		);
	}

	/**
	 * Test: warn when another redirect-handling plugin is active.
	 *
	 * Two plugins handling 404s tends to produce duplicate logging,
	 * unpredictable redirect precedence and the occasional loop —
	 * never the kind of bug a user enjoys debugging.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function test_conflicting_plugins(): array {
		$conflicts = $this->detect_conflicting_plugins();

		if ( empty( $conflicts ) ) {
			return $this->build_result(
				'good',
				'd404_conflicting_plugin',
				__( 'No conflicting redirect plugins detected', '404-to-301' ),
				__( 'Nothing else on this site is fighting 404 to 301 for control of redirects.', '404-to-301' )
			);
		}

		return $this->build_result(
			'recommended',
			'd404_conflicting_plugin',
			__( 'Another redirect plugin is active', '404-to-301' ),
			sprintf(
				/* translators: %s: comma-separated plugin names. */
				__( '404 to 301 detected another redirect-handling plugin running alongside it: %s. Running two redirect plugins can cause duplicate logging, redirect loops or unpredictable precedence. Disable one of them if the duplication is unintentional.', '404-to-301' ),
				esc_html( implode( ', ', $conflicts ) )
			)
		);
	}

	/**
	 * Test: redirect is enabled but logging is off (or vice-versa) —
	 * surface the inconsistency so the user knows.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function test_logging_state(): array {
		$settings = Core::instance()->settings();

		if ( ! $settings ) {
			return $this->build_result(
				'good',
				'd404_logging_state',
				__( '404 to 301 logging state', '404-to-301' ),
				__( 'Settings are not yet initialised.', '404-to-301' )
			);
		}

		$logs_on     = (bool) $settings->get( 'logs_enabled', true );
		$redirect_on = (bool) $settings->get( 'redirect_enabled', true );

		if ( $redirect_on && ! $logs_on ) {
			return $this->build_result(
				'recommended',
				'd404_logging_state',
				__( '404 logging is disabled', '404-to-301' ),
				__( '404 to 301 is actively redirecting visitors away from broken URLs, but logging is turned off — you have no visibility into which URLs are 404ing. Re-enable logging on the Settings page if you want to see what is breaking.', '404-to-301' ),
				__( 'Open Settings', '404-to-301' ),
				Plugin::get_url( 'settings' )
			);
		}

		return $this->build_result(
			'good',
			'd404_logging_state',
			__( '404 to 301 logging is healthy', '404-to-301' ),
			__( 'Logging and redirect settings are consistent.', '404-to-301' )
		);
	}

	/**
	 * Test: did the DB migration finish?
	 *
	 * `db_version` lagging behind `D404_DB_VERSION` means the upgrade
	 * routine bailed partway and the schema is mid-flight.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function test_db_version(): array {
		$settings   = Core::instance()->settings();
		$stored_ver = $settings ? (string) $settings->get( 'db_version', '' ) : '';
		$expected   = defined( 'D404_DB_VERSION' ) ? D404_DB_VERSION : '';

		if ( '' === $stored_ver || '' === $expected || version_compare( $stored_ver, $expected, '>=' ) ) {
			return $this->build_result(
				'good',
				'd404_db_version',
				__( '404 to 301 database schema is up to date', '404-to-301' ),
				__( 'The plugin database tables match the expected schema version.', '404-to-301' )
			);
		}

		return $this->build_result(
			'critical',
			'd404_db_version',
			__( '404 to 301 database schema is out of date', '404-to-301' ),
			sprintf(
				/* translators: 1: stored DB version, 2: expected DB version. */
				__( 'The stored database schema version (%1$s) is older than the version this plugin expects (%2$s). A previous upgrade may have been interrupted. Visit any 404 to 301 admin page to trigger the upgrader again.', '404-to-301' ),
				esc_html( $stored_ver ),
				esc_html( $expected )
			)
		);
	}

	// --------------------------------------------------------------------- //
	// Helpers.
	// --------------------------------------------------------------------- //

	/**
	 * Shared cron-health evaluator used by every cron-shaped test.
	 *
	 * @since 4.0.0
	 *
	 * @param string $test_id      Test identifier.
	 * @param string $hook         Cron hook name.
	 * @param string $display_name Human-readable name of the cron's owner.
	 * @param string $settings_url Where to send the user to fix it.
	 *
	 * @return array
	 */
	private function cron_health_result( string $test_id, string $hook, string $display_name, string $settings_url ): array {
		$next = wp_next_scheduled( $hook );

		if ( ! $next ) {
			return $this->build_result(
				'recommended',
				$test_id,
				sprintf(
					/* translators: %s: addon name. */
					__( '%s cron is not scheduled', '404-to-301' ),
					$display_name
				),
				sprintf(
					/* translators: %s: addon name. */
					__( 'The %s cron event is missing from WP-Cron. This usually means the addon was deactivated mid-cycle or another plugin cleared the schedule. Re-saving the addon settings will reschedule it.', '404-to-301' ),
					$display_name
				),
				__( 'Open Settings', '404-to-301' ),
				$settings_url
			);
		}

		$schedules = wp_get_schedules();
		$interval  = (int) ( $schedules[ wp_get_schedule( $hook ) ]['interval'] ?? 0 );
		$overdue   = $interval > 0 && ( time() - $next ) > ( $interval * self::CRON_OVERDUE_MULTIPLIER );

		if ( ! $overdue ) {
			return $this->build_result(
				'good',
				$test_id,
				sprintf(
					/* translators: %s: addon name. */
					__( '%s cron is healthy', '404-to-301' ),
					$display_name
				),
				sprintf(
					/* translators: 1: addon name, 2: next-run timestamp. */
					__( 'The %1$s cron is scheduled and on time. Next run: %2$s.', '404-to-301' ),
					$display_name,
					esc_html( $this->format_timestamp( $next ) )
				)
			);
		}

		return $this->build_result(
			'critical',
			$test_id,
			sprintf(
				/* translators: %s: addon name. */
				__( '%s cron is overdue', '404-to-301' ),
				$display_name
			),
			sprintf(
				/* translators: 1: addon name, 2: scheduled-for timestamp. */
				__( 'The %1$s cron event was due at %2$s but has not run. WP-Cron only fires when your site receives traffic; if traffic is low or DISABLE_WP_CRON is set without a real cron job, scheduled tasks will stall. Configure a real system cron, or check for a plugin blocking WP-Cron.', '404-to-301' ),
				$display_name,
				esc_html( $this->format_timestamp( $next ) )
			),
			__( 'Open Settings', '404-to-301' ),
			$settings_url
		);
	}

	/**
	 * Detect other active plugins that also handle redirects.
	 *
	 * @since 4.0.0
	 *
	 * @return string[] Plugin display names that conflict.
	 */
	private function detect_conflicting_plugins(): array {
		$candidates = array(
			'redirection/redirection.php'             => 'Redirection',
			'safe-redirect-manager/safe-redirect-manager.php' => 'Safe Redirect Manager',
			'simple-301-redirects/wpsimple301redirects.php' => 'Simple 301 Redirects',
			'eps-301-redirects/eps-301-redirects.php' => '301 Redirects',
			'quick-pagepost-redirect-plugin/page_post_redirect_plugin.php' => 'Quick Page/Post Redirect',
		);

		$this->ensure_plugin_api();

		return array_values(
			array_filter(
				$candidates,
				fn( string $basename ): bool => is_plugin_active( $basename ),
				ARRAY_FILTER_USE_KEY
			)
		);
	}

	/**
	 * Comma-separated list of detected conflicts, or "None".
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	private function conflicting_plugins_label(): string {
		$conflicts = $this->detect_conflicting_plugins();

		if ( empty( $conflicts ) ) {
			return __( 'None detected', '404-to-301' );
		}

		return implode( ', ', $conflicts );
	}

	/**
	 * Is the Logs Cleaner addon active?
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function is_cleaner_active(): bool {
		$this->ensure_plugin_api();

		return is_plugin_active( self::CLEANER_BASENAME );
	}

	/**
	 * Make sure `is_plugin_active()` is loaded.
	 *
	 * The Plugin API is only auto-loaded on `wp-admin/plugins.php`; Site
	 * Health renders from every admin screen, so we may hit this code
	 * path before WP has pulled the file in.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function ensure_plugin_api(): void {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	}

	/**
	 * Row count for one of our tables.
	 *
	 * @since 4.0.0
	 *
	 * @param string $unprefixed Unprefixed table name, e.g. `404_to_301_logs`.
	 *
	 * @return int
	 */
	private function row_count( string $unprefixed ): int {
		global $wpdb;

		$table = $wpdb->prefix . $unprefixed;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name cannot be parameterised; it is built from a hard-coded literal plus `$wpdb->prefix`.
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}`" );
	}

	/**
	 * Total disk size (data + indexes) of one of our tables, in bytes.
	 *
	 * Queries `information_schema.TABLES`; returns 0 on hosts that
	 * restrict access to the schema.
	 *
	 * @since 4.0.0
	 *
	 * @param string $unprefixed Unprefixed table name.
	 *
	 * @return int
	 */
	private function table_size_bytes( string $unprefixed ): int {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$size = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT (data_length + index_length) FROM information_schema.TABLES WHERE table_schema = %s AND table_name = %s',
				DB_NAME,
				$wpdb->prefix . $unprefixed
			)
		);

		return (int) $size;
	}

	/**
	 * Render `wp_next_scheduled()` as a localised timestamp or a dash.
	 *
	 * @since 4.0.0
	 *
	 * @param string $hook Cron hook name.
	 *
	 * @return string
	 */
	private function format_next_run( string $hook ): string {
		$next = wp_next_scheduled( $hook );

		return $next ? $this->format_timestamp( (int) $next ) : __( 'Not scheduled', '404-to-301' );
	}

	/**
	 * Format a UTC unix timestamp in the site's timezone.
	 *
	 * @since 4.0.0
	 *
	 * @param int $timestamp Unix timestamp.
	 *
	 * @return string
	 */
	private function format_timestamp( int $timestamp ): string {
		$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		return (string) wp_date( $format, $timestamp );
	}

	/**
	 * Localised Yes / No label.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $flag Boolean to render.
	 *
	 * @return string
	 */
	private function yes_no( bool $flag ): string {
		return $flag ? __( 'Yes', '404-to-301' ) : __( 'No', '404-to-301' );
	}

	/**
	 * Build a Site Health test result array.
	 *
	 * The badge colour is derived from the status — Site Health uses
	 * blue for healthy results, orange for warnings, red for critical —
	 * so callers only ever pick the status, never the colour.
	 *
	 * @since 4.0.0
	 *
	 * @param string $status       One of `good`, `recommended`, `critical`.
	 * @param string $test_id      Test identifier.
	 * @param string $label        Short label.
	 * @param string $description  Long description (plain text — will be escaped).
	 * @param string $action_label Optional CTA label.
	 * @param string $action_url   Optional CTA URL.
	 *
	 * @return array
	 */
	private function build_result( string $status, string $test_id, string $label, string $description, string $action_label = '', string $action_url = '' ): array {
		$colors = array(
			'good'        => 'blue',
			'recommended' => 'orange',
			'critical'    => 'red',
		);

		$result = array(
			'label'       => $label,
			'status'      => $status,
			'badge'       => array(
				'label' => __( '404 to 301', '404-to-301' ),
				'color' => $colors[ $status ] ?? 'blue',
			),
			'description' => sprintf( '<p>%s</p>', esc_html( $description ) ),
			'test'        => $test_id,
		);

		if ( '' !== $action_label && '' !== $action_url ) {
			$result['actions'] = sprintf(
				'<p><a class="button button-primary" href="%1$s">%2$s</a></p>',
				esc_url( $action_url ),
				esc_html( $action_label )
			);
		}

		return $result;
	}
}
