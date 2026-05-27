import { __ } from '@wordpress/i18n'
import {
	Button,
	Flex,
	FlexItem,
	Notice,
	__experimentalText as Text,
} from '@wordpress/components'
import useMigration from '../../hooks/use-migration'

/**
 * Migration banner.
 *
 * Renders one of three states (see the v4 plan, §6):
 *
 *   A — Action Scheduler available
 *   B — AS not available, user can install_plugins → show install link
 *   C — AS not available, install_plugins not allowed → chunked AJAX only
 *
 * Hidden when there's nothing to migrate.
 */
const MigrationBanner = () => {
	const { status, isStarting, start, abort } = useMigration()

	if (!status) {
		return null
	}

	// Nothing to migrate — no banner.
	if (!status.legacy_present || status.logs_migrated) {
		return null
	}

	// In-flight: progress + abort.
	if (status.running) {
		return (
			<div className="d404-migration-banner">
				<Notice status="info" isDismissible={false}>
					<Flex justify="space-between" gap={3}>
						<FlexItem>
							<Text>
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
							</Text>
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
		<div className="d404-migration-banner">
			<Notice status="warning" isDismissible={false}>
				<p>
					<strong>
						{__(
							'Legacy 404 to 301 data detected',
							'404-to-301',
						)}
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
					<p style={{ marginTop: '1em', fontSize: '0.85em', opacity: 0.75 }}>
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
