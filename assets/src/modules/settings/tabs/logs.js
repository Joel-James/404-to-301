import { __ } from '@wordpress/i18n'
import {
	Notice,
	PanelBody,
	PanelRow,
	ToggleControl,
} from '@wordpress/components'
import { applyFilters } from '@wordpress/hooks'
import useSettings from '../../../hooks/use-settings'

/**
 * Default cross-sell Notice promoting the Logs Cleaner addon.
 *
 * Lives at the bottom of the Error logs panel and is routed through
 * the `d404.settings.logs.cross_sell` filter so the addon — which is
 * loaded on this same screen — returns `null` to hide it once active.
 *
 * The CTA is an inline text link in the body copy rather than the
 * Notice `actions` button: the button forces the banner to a taller
 * two-row layout, so a plain link keeps the notice compact.
 *
 * The Notice is non-dismissible: a dismissible CTA would persist its
 * dismissed state in component memory only (no server round-trip), so
 * it'd come straight back on the next page load. Better to render a
 * stable banner the addon can swap out entirely.
 */
const DefaultCrossSell = () => (
	<PanelRow className="d404-cross-sell">
		<Notice status="info" isDismissible={false}>
			<p className="d404-cross-sell__title">
				<strong>{__('Drowning in 404 logs?', '404-to-301')}</strong>
			</p>
			<p>
				{__(
					'Install the Logs Cleaner addon to auto-prune the 404 log table — by age, by row count, or on a fixed schedule. Set it once and the table stays small on its own.',
					'404-to-301',
				)}{' '}
				<a
					href="https://duckdev.com/addon/404-to-301-logs-cleaner/"
					target="_blank"
					rel="noreferrer"
				>
					{__('Get Logs Cleaner', '404-to-301')}
				</a>
			</p>
		</Notice>
	</PanelRow>
)

const LogsTab = () => {
	const { getSetting, setSetting } = useSettings()

	/*
	 * Addon extension point. Addons (eg. Logs Cleaner) hook into
	 * `d404.settings.logs.fields` via `@wordpress/hooks` and return
	 * one or more React nodes that are rendered at the end of the
	 * Error logs PanelBody. The filter receives the `getSetting` /
	 * `setSetting` accessors so the injected controls read and write
	 * through the same hook the built-in fields use.
	 *
	 * Note: the hook name must start with a letter — `@wordpress/hooks`
	 * rejects names that lead with a digit, so we use the `d404`
	 * prefix here instead of `404_to_301`.
	 */
	const extra = applyFilters('d404.settings.logs.fields', null, {
		getSetting,
		setSetting,
	})

	/*
	 * Sibling-panel slot. Unlike `…fields` (which injects rows *inside*
	 * the Error-logs box above), this renders *below* it as a separate
	 * PanelBody sibling. The Logs Cleaner addon adds its own auto-prune
	 * box here. Receives the same accessors so injected panels read /
	 * write through the same hook.
	 */
	const afterPanels = applyFilters('d404.settings.logs.after', null, {
		getSetting,
		setSetting,
	})

	/*
	 * Cross-sell slot. Defaults to <DefaultCrossSell />; addons return
	 * `null` (or their own node) to suppress / replace it. Kept as a
	 * separate filter from `…fields` so an addon that wants to inject
	 * fields *without* hiding the promo can do so.
	 */
	const crossSell = applyFilters(
		'd404.settings.logs.cross_sell',
		<DefaultCrossSell />,
	)

	return (
		<>
			<PanelBody title={__('404 Logs', '404-to-301')}>
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
				{extra}
				{crossSell}
			</PanelBody>

			{afterPanels}
		</>
	)
}

export default LogsTab
