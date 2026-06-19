/* global d404 */

import { __ } from '@wordpress/i18n'
import { Button, Flex, FlexItem, Notice } from '@wordpress/components'
import useMigration from '../../hooks/use-migration'

/**
 * Outer banner component — only mounts the live banner when the
 * server-side hint says there's something to migrate.
 *
 * The localised `d404.migrationPending` flag is set by
 * `Admin\Assets::script_vars()` from the cheap `logs_migrated`
 * option. When it's `false` we render nothing AND skip mounting
 * `<LiveBanner />`, which means `useMigration` never runs and the
 * `GET /migration` round-trip never fires — important because the
 * Logs page renders the banner on every load.
 *
 * @return {JSX.Element|null}
 */
const MigrationBanner = () => {
	const pending =
		typeof d404 !== 'undefined' ? d404.migrationPending !== false : true

	if (!pending) {
		return null
	}

	return <LiveBanner />
}

/**
 * Inner banner. Owns the `useMigration` hook and renders one of
 * three states (see the v4 plan, §6):
 *
 *   A — Action Scheduler available
 *   B — AS not available, user can install_plugins → show install link
 *   C — AS not available, install_plugins not allowed → chunked AJAX only
 *
 * Hoisted into a child component so the hook always runs in the
 * same call order whenever this tree mounts.
 */
const LiveBanner = () => {
	const { status, isStarting, justCompleted, start, abort } = useMigration()

	if (!status) {
		return null
	}

	// Just finished in this session — confirm success and point the
	// admin at a reload, since the logs table won't show the migrated
	// rows until the page is fetched again. Checked before the
	// "nothing to migrate" guard below because completion flips
	// `logs_migrated` true, which that guard would otherwise swallow.
	if (justCompleted) {
		return (
			<div className="d404-migration-banner" key="migration-done">
				<Notice
					status="success"
					isDismissible={false}
					spokenMessage={__('Migration complete.', '404-to-301')}
				>
					<Flex justify="space-between" gap={3}>
						<FlexItem>
							<span>
								<strong>
									{__('Migration complete.', '404-to-301')}
								</strong>{' '}
								{__(
									'Your old 404 logs have been moved. Reload the page to see them.',
									'404-to-301',
								)}
							</span>
						</FlexItem>
						<FlexItem>
							<Button
								variant="primary"
								onClick={() => window.location.reload()}
							>
								{__('Reload page', '404-to-301')}
							</Button>
						</FlexItem>
					</Flex>
				</Notice>
			</div>
		)
	}

	// Nothing to migrate — no banner.
	if (!status.legacy_present || status.logs_migrated) {
		return null
	}

	// In-flight: progress + abort.
	if (status.running) {
		return (
			// `key` keeps this branch a distinct element from the idle
			// one below: when the banner flips idle → running, React
			// remounts the <Notice> instead of updating it in place.
			// Updating a @wordpress/components <Notice> across that
			// change throws inside its render (it serialises the element
			// children for the screen-reader announcement, and the
			// in-place update trips a `.length` read on undefined). A
			// remount takes the same code path as a fresh page load,
			// which renders cleanly. The explicit string `spokenMessage`
			// also stops Notice from serialising the child tree at all.
			<div className="d404-migration-banner" key="migration-running">
				<Notice
					status="info"
					isDismissible={false}
					spokenMessage={__(
						'Migrating old 404 logs in the background…',
						'404-to-301',
					)}
				>
					<Flex justify="space-between" gap={3}>
						<FlexItem>
							<span>
								{__(
									'Migrating old 404 logs in the background…',
									'404-to-301',
								)}{' '}
								<strong>
									{status.remaining}{' '}
									{__('rows remaining', '404-to-301')}
								</strong>
								{status.has_as &&
									` · ${__(
										'Using Action Scheduler',
										'404-to-301',
									)}`}
							</span>
						</FlexItem>
						<FlexItem>
							<Button variant="secondary" onClick={abort}>
								{__('Abort migration', '404-to-301')}
							</Button>
						</FlexItem>
					</Flex>
				</Notice>
			</div>
		)
	}

	// Idle but legacy table present — invite the admin to start.
	return (
		// Distinct `key` from the running branch above — see the note
		// there. Forces a remount (not an in-place update) on the
		// idle → running flip, sidestepping the Notice render crash.
		<div className="d404-migration-banner" key="migration-idle">
			<Notice
				status="warning"
				isDismissible={false}
				spokenMessage={__(
					'Legacy 404 to 301 data detected',
					'404-to-301',
				)}
			>
				<p>
					<strong>
						{__('Legacy 404 to 301 data detected', '404-to-301')}
					</strong>
				</p>
				<p>
					{__(
						'There are old 404 logs from the previous version of the plugin that have not been migrated yet.',
						'404-to-301',
					)}{' '}
					<strong>{status.remaining}</strong>{' '}
					{__('rows are waiting to be moved.', '404-to-301')}
				</p>

				<Flex justify="flex-start" gap={3} wrap>
					<FlexItem>
						<Button
							variant="primary"
							isBusy={isStarting}
							disabled={isStarting}
							onClick={start}
						>
							{isStarting
								? __('Starting…', '404-to-301')
								: __('Start migration', '404-to-301')}
						</Button>
					</FlexItem>

					{!status.has_as && status.install_as_url && (
						<FlexItem>
							<Button
								variant="secondary"
								href={status.install_as_url}
							>
								{__(
									'Install Action Scheduler (faster, optional)',
									'404-to-301',
								)}
							</Button>
						</FlexItem>
					)}

					<FlexItem>
						<Button
							variant="tertiary"
							onClick={abort}
							isDestructive
						>
							{__('Skip migration', '404-to-301')}
						</Button>
					</FlexItem>
				</Flex>

				{!status.has_as && (
					<p
						style={{
							marginTop: '1em',
							fontSize: '0.85em',
							opacity: 0.75,
						}}
					>
						{__(
							'Action Scheduler is recommended for sites with many 404 logs — it runs the migration in the background with retries. The migration still works without it, processing rows in chunks while you stay on this page.',
							'404-to-301',
						)}
					</p>
				)}
			</Notice>
		</div>
	)
}

export default MigrationBanner
