<?php
/**
 * `wp 404-to-301 doctor` — health check for the plugin.
 *
 * Prints grouped check results and exits non-zero when any FAIL
 * conditions are detected, so the command can be wired into CI / ops
 * scripts as a tripwire. Pattern is borrowed from `wc doctor`: every
 * check returns a `(status, message)` tuple and the runner takes care
 * of grouping, formatting, and tallying.
 *
 * Addons can register their own checks via the
 * `404_to_301_doctor_checks` filter.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\CLI;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Settings as SettingsModel;
use WP_CLI;

/**
 * Class Doctor
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour\CLI
 */
class Doctor extends Command {

	/**
	 * Per-row status constants. Plain strings so the filter contract
	 * survives a `var_export` round-trip and is friendly to addons in
	 * non-PHP languages (eg. when consumed via the WP-CLI JSON path).
	 *
	 * @since 4.0.0
	 */
	const PASS = 'pass';
	const WARN = 'warn';
	const FAIL = 'fail';

	/**
	 * Register the subcommand.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function register(): void {
		WP_CLI::add_command( '404-to-301 doctor', static::class );
	}

	/**
	 * Run the health-check suite.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output format. Defaults to a human-readable grouped report.
	 * ---
	 * default: report
	 * options:
	 *   - report
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp 404-to-301 doctor
	 *     wp 404-to-301 doctor --format=json
	 *
	 * @since 4.0.0
	 *
	 * @param array $args  Positional args (unused).
	 * @param array $assoc Assoc args.
	 *
	 * @return void
	 */
	public function __invoke( array $args, array $assoc ): void {
		unset( $args );

		$format = (string) ( $assoc['format'] ?? 'report' );

		$groups = array(
			'cron'     => array(
				'label'  => 'Cron',
				'checks' => $this->check_cron(),
			),
			'settings' => array(
				'label'  => 'Settings',
				'checks' => $this->check_settings(),
			),
			'database' => array(
				'label'  => 'Database',
				'checks' => $this->check_database(),
			),
		);

		/**
		 * Filter the grouped check list before rendering.
		 *
		 * Addons should append their own group (keyed by a unique
		 * slug) rather than mutating existing ones. Each group is an
		 * array with `label` and `checks` keys; each check is a
		 * `[ 'status' => …, 'message' => … ]` tuple where `status` is
		 * one of `pass`, `warn`, `fail`.
		 *
		 * @since 4.0.0
		 *
		 * @param array $groups Group => [label, checks[]] map.
		 */
		$groups = (array) apply_filters( '404_to_301_doctor_checks', $groups );

		if ( 'report' === $format ) {
			$this->render_report( $groups );
		} else {
			$this->render_machine( $groups, $format );
		}

		// Exit non-zero on FAIL so the command is usable in scripts.
		// WARN doesn't trip the exit code — it's a heads-up, not a
		// breakage — but we surface the counts in the summary line.
		$has_fail = false;
		foreach ( $groups as $group ) {
			foreach ( (array) ( $group['checks'] ?? array() ) as $check ) {
				if ( self::FAIL === ( $check['status'] ?? '' ) ) {
					$has_fail = true;
					break 2;
				}
			}
		}

		if ( $has_fail ) {
			WP_CLI::halt( 1 );
		}
	}

	/**
	 * Cron group: surface the scheduled events that belong to this
	 * plugin (and its addons), with the next-run timestamp so ops can
	 * see at a glance whether cron is wedged.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array{status:string, message:string}>
	 */
	private function check_cron(): array {
		$prefixes = array( '404_to_301_' );

		/**
		 * Filter the hook-name prefixes the doctor uses to identify
		 * plugin-owned cron events. Addons that schedule jobs under a
		 * different prefix (eg. `logs_cleaner_…`) can register it
		 * here so they show up in the doctor output too.
		 *
		 * @since 4.0.0
		 *
		 * @param string[] $prefixes Prefix list.
		 */
		$prefixes = (array) apply_filters( '404_to_301_doctor_cron_prefixes', $prefixes );

		$cron = _get_cron_array(); // phpcs:ignore WordPress.WP.AlternativeFunctions.cron__get_cron_array -- this is the documented inspection API.

		if ( ! is_array( $cron ) || empty( $cron ) ) {
			return array(
				array(
					'status'  => self::PASS,
					'message' => 'No WP-Cron events are scheduled at all on this site.',
				),
			);
		}

		$found = array();
		foreach ( $cron as $timestamp => $hooks ) {
			foreach ( (array) $hooks as $hook => $_ ) {
				if ( ! $this->prefix_match( (string) $hook, $prefixes ) ) {
					continue;
				}

				$found[] = array(
					'status'  => self::PASS,
					'message' => sprintf(
						'Hook %s next runs %s (%s).',
						(string) $hook,
						gmdate( 'Y-m-d H:i:s', (int) $timestamp ),
						$this->relative_time( (int) $timestamp )
					),
				);
			}
		}

		if ( empty( $found ) ) {
			$found[] = array(
				'status'  => self::PASS,
				'message' => 'No plugin-owned cron events scheduled.',
			);
		}

		return $found;
	}

	/**
	 * Settings group: walk a handful of settings that are commonly
	 * wrong on freshly-imported / staging-cloned sites.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array{status:string, message:string}>
	 */
	private function check_settings(): array {
		$settings = SettingsModel::instance()->all();
		$checks   = array();

		// Email recipients: when email_enabled is on, the recipient
		// list should not be empty and every entry should pass
		// is_email(). An empty recipient with email_enabled = true is
		// the silent-failure mode we want to surface loudest.
		$email_enabled = (bool) ( $settings['email_enabled'] ?? false );
		$recipients    = (array) ( $settings['email_recipient'] ?? array() );

		if ( $email_enabled ) {
			if ( empty( $recipients ) ) {
				$checks[] = array(
					'status'  => self::FAIL,
					'message' => 'Email notifications are enabled but no recipients are set.',
				);
			} else {
				$invalid = array();
				foreach ( $recipients as $email ) {
					if ( ! is_email( (string) $email ) ) {
						$invalid[] = (string) $email;
					}
				}

				if ( ! empty( $invalid ) ) {
					$checks[] = array(
						'status'  => self::FAIL,
						'message' => sprintf(
							'Invalid email recipient(s): %s.',
							implode( ', ', $invalid )
						),
					);
				} else {
					$checks[] = array(
						'status'  => self::PASS,
						'message' => sprintf( 'Email recipients look valid (%d configured).', count( $recipients ) ),
					);
				}
			}
		}

		// Default redirect: when redirect_enabled is on, the resolved
		// target should exist. We don't check reachability over the
		// network — only that the configured value parses as a URL or
		// resolves to a real post.
		$redirect_enabled = (bool) ( $settings['redirect_enabled'] ?? false );
		$target           = (string) ( $settings['redirect_target'] ?? '' );

		if ( $redirect_enabled ) {
			if ( 'link' === $target ) {
				$url = (string) ( $settings['redirect_link'] ?? '' );
				if ( '' === $url ) {
					$checks[] = array(
						'status'  => self::FAIL,
						'message' => 'Default redirect is set to a custom URL but the URL is empty.',
					);
				} elseif ( ! wp_http_validate_url( $url ) ) {
					$checks[] = array(
						'status'  => self::FAIL,
						'message' => sprintf( 'Default redirect URL is not a valid URL: %s.', $url ),
					);
				} else {
					$checks[] = array(
						'status'  => self::PASS,
						'message' => sprintf( 'Default redirect URL parses cleanly (%s).', $url ),
					);
				}
			} elseif ( 'page' === $target ) {
				$page_id = (int) ( $settings['redirect_page'] ?? 0 );
				if ( $page_id <= 0 ) {
					$checks[] = array(
						'status'  => self::FAIL,
						'message' => 'Default redirect is set to a page but no page ID is configured.',
					);
				} else {
					$post = get_post( $page_id );
					if ( ! $post || 'publish' !== $post->post_status ) {
						$checks[] = array(
							'status'  => self::FAIL,
							'message' => sprintf( 'Default redirect target page #%d does not exist or is not published.', $page_id ),
						);
					} else {
						$checks[] = array(
							'status'  => self::PASS,
							'message' => sprintf( 'Default redirect target page #%d resolves (%s).', $page_id, get_the_title( $post ) ),
						);
					}
				}
			}
		}

		if ( empty( $checks ) ) {
			$checks[] = array(
				'status'  => self::PASS,
				'message' => 'Notifications and the default redirect are both off — nothing to validate.',
			);
		}

		return $checks;
	}

	/**
	 * Database group: surface schema-level inconsistencies that the
	 * UI can't fix on its own.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int, array{status:string, message:string}>
	 */
	private function check_database(): array {
		global $wpdb;

		$checks = array();

		$logs      = $wpdb->prefix . '404_to_301_logs';
		$redirects = $wpdb->prefix . '404_to_301_redirects';

		// Tables exist.
		foreach ( array( $logs, $redirects ) as $table ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
			$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
			if ( $exists !== $table ) {
				$checks[] = array(
					'status'  => self::FAIL,
					'message' => sprintf( 'Table %s is missing.', $table ),
				);
			} else {
				$checks[] = array(
					'status'  => self::PASS,
					'message' => sprintf( 'Table %s exists.', $table ),
				);
			}
		}

		// Orphaned `redirect_id` references on log rows: rows that
		// point at a redirect that has since been deleted. The UI
		// would render these as "Custom redirect (gone)" — better to
		// flag them.
		// Table names come from our own BerlinDB schemas — there is no
		// user input here, only trusted identifiers, so the interpolation
		// is intentional and safe.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$orphans = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$logs} l
			LEFT JOIN {$redirects} r ON r.id = l.redirect_id
			WHERE l.redirect_id IS NOT NULL AND r.id IS NULL"
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( $orphans > 0 ) {
			$checks[] = array(
				'status'  => self::WARN,
				'message' => sprintf( '%d log row(s) reference a redirect that no longer exists.', $orphans ),
			);
		} else {
			$checks[] = array(
				'status'  => self::PASS,
				'message' => 'No orphaned redirect references on log rows.',
			);
		}

		// Duplicate `source_hash` on the redirects table. There's a
		// UNIQUE index so the only way to get here is a manual SQL
		// poke at the table; surfacing it tells you something is off.
		// Same as above — the only interpolated value is the trusted
		// table name from our own schema.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$dupes = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM (
				SELECT source_hash FROM {$redirects} GROUP BY source_hash HAVING COUNT(*) > 1
			) AS d"
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( $dupes > 0 ) {
			$checks[] = array(
				'status'  => self::FAIL,
				'message' => sprintf( '%d duplicate source_hash value(s) on the redirects table.', $dupes ),
			);
		} else {
			$checks[] = array(
				'status'  => self::PASS,
				'message' => 'Redirect source hashes are unique.',
			);
		}

		return $checks;
	}

	/**
	 * Render the grouped report in the default human-readable shape.
	 *
	 * @since 4.0.0
	 *
	 * @param array $groups Group => [label, checks[]] map.
	 *
	 * @return void
	 */
	private function render_report( array $groups ): void {
		$summary = array(
			self::PASS => 0,
			self::WARN => 0,
			self::FAIL => 0,
		);

		foreach ( $groups as $group ) {
			$label  = (string) ( $group['label'] ?? '' );
			$checks = (array) ( $group['checks'] ?? array() );

			WP_CLI::log( '' );
			WP_CLI::log( WP_CLI::colorize( "%B{$label}%n" ) );

			if ( empty( $checks ) ) {
				WP_CLI::log( '  (no checks)' );
				continue;
			}

			foreach ( $checks as $check ) {
				$status = (string) ( $check['status'] ?? self::PASS );
				$msg    = (string) ( $check['message'] ?? '' );

				if ( isset( $summary[ $status ] ) ) {
					++$summary[ $status ];
				}

				WP_CLI::log( '  ' . $this->badge( $status ) . ' ' . $msg );
			}
		}

		WP_CLI::log( '' );
		WP_CLI::log(
			sprintf(
				'Summary: %d pass, %d warn, %d fail.',
				$summary[ self::PASS ],
				$summary[ self::WARN ],
				$summary[ self::FAIL ]
			)
		);
	}

	/**
	 * Render the report as a flat list for machine consumption (table
	 * / csv / json / yaml).
	 *
	 * @since 4.0.0
	 *
	 * @param array  $groups Group => [label, checks[]] map.
	 * @param string $format One of table / csv / json / yaml.
	 *
	 * @return void
	 */
	private function render_machine( array $groups, string $format ): void {
		$rows = array();
		foreach ( $groups as $slug => $group ) {
			$label = (string) ( $group['label'] ?? $slug );
			foreach ( (array) ( $group['checks'] ?? array() ) as $check ) {
				$rows[] = array(
					'group'   => $label,
					'status'  => (string) ( $check['status'] ?? self::PASS ),
					'message' => (string) ( $check['message'] ?? '' ),
				);
			}
		}

		$this->print_rows( array( 'format' => $format ), $rows, array( 'group', 'status', 'message' ) );
	}

	/**
	 * Format a status badge with ANSI colouring for the report view.
	 *
	 * @since 4.0.0
	 *
	 * @param string $status One of pass / warn / fail.
	 *
	 * @return string
	 */
	private function badge( string $status ): string {
		switch ( $status ) {
			case self::FAIL:
				return WP_CLI::colorize( '%R[FAIL]%n' );

			case self::WARN:
				return WP_CLI::colorize( '%Y[WARN]%n' );

			case self::PASS:
			default:
				return WP_CLI::colorize( '%G[PASS]%n' );
		}
	}

	/**
	 * `in 2 hours` / `42 minutes ago` style label.
	 *
	 * @since 4.0.0
	 *
	 * @param int $timestamp Unix timestamp.
	 *
	 * @return string
	 */
	private function relative_time( int $timestamp ): string {
		$now = time();
		if ( $timestamp >= $now ) {
			return 'in ' . human_time_diff( $now, $timestamp );
		}

		return human_time_diff( $timestamp, $now ) . ' ago';
	}

	/**
	 * True when `$candidate` starts with any of the prefixes.
	 *
	 * @since 4.0.0
	 *
	 * @param string   $candidate String to test.
	 * @param string[] $prefixes  Prefixes to match.
	 *
	 * @return bool
	 */
	private function prefix_match( string $candidate, array $prefixes ): bool {
		foreach ( $prefixes as $prefix ) {
			$prefix = (string) $prefix;
			if ( '' !== $prefix && 0 === strpos( $candidate, $prefix ) ) {
				return true;
			}
		}

		return false;
	}
}
