import { __ } from '@wordpress/i18n'
import {
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl,
} from '@wordpress/components'
import useSettings from '../../../hooks/use-settings'

const LogsTab = () => {
	const { getSetting, setSetting } = useSettings()

	return (
		<PanelBody title={__('Error logs', '404-to-301')}>
			<PanelRow>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Log 404 errors', '404-to-301')}
					help={__(
						'Save every 404 hit to the database so you can review and fix them from the Logs page.',
						'404-to-301',
					)}
					checked={!!getSetting('logs_enabled', true)}
					onChange={(v) => setSetting('logs_enabled', v)}
				/>
			</PanelRow>
			<PanelRow>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Skip search engine bots', '404-to-301')}
					help={__(
						'Skip 404s from obvious crawlers (bot, spider, slurp, …) to keep the log table small.',
						'404-to-301',
					)}
					checked={!!getSetting('logs_skip_bots', true)}
					onChange={(v) => setSetting('logs_skip_bots', v)}
				/>
			</PanelRow>
			<PanelRow>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Skip duplicate URLs', '404-to-301')}
					help={__(
						'When on, repeat 404s on the same URL do not bump the hit counter. Useful when you only want the first occurrence.',
						'404-to-301',
					)}
					checked={!!getSetting('logs_skip_duplicates', false)}
					onChange={(v) => setSetting('logs_skip_duplicates', v)}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					type="number"
					min={0}
					label={__('Retention (days)', '404-to-301')}
					help={__(
						'Auto-prune logs older than this many days. Set to 0 to keep forever.',
						'404-to-301',
					)}
					value={getSetting('logs_retention_days', 0)}
					onChange={(v) =>
						setSetting(
							'logs_retention_days',
							Math.max(0, parseInt(v, 10) || 0),
						)
					}
				/>
			</PanelRow>
		</PanelBody>
	)
}

export default LogsTab
