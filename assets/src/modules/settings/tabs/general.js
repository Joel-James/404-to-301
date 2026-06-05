import { __ } from '@wordpress/i18n'
import {
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
} from '@wordpress/components'
import { applyFilters } from '@wordpress/hooks'
import useSettings from '../../../hooks/use-settings'

const General = () => {
	const { getSetting, setSetting } = useSettings()

	/*
	 * Addon extension point. Addons hook into
	 * `d404.settings.general.fields` via `@wordpress/hooks` and return
	 * one or more React nodes rendered at the end of the General tab.
	 * The filter receives the `getSetting` / `setSetting` accessors so
	 * the injected controls read and write through the same hook the
	 * built-in fields use.
	 *
	 * Note: the hook name must start with a letter — `@wordpress/hooks`
	 * rejects names that lead with a digit, so we use the `d404` prefix
	 * here instead of `404_to_301`.
	 */
	const extra = applyFilters(
		'd404.settings.general.fields',
		null,
		{ getSetting, setSetting },
	)

	/*
	 * Cross-sell slot. No default promo today, but the filter exists so
	 * addons can inject (or replace) one without a future parent-side
	 * code change.
	 */
	const crossSell = applyFilters('d404.settings.general.cross_sell', null)

	return (
		<>
			<PanelBody title={__('Behaviour', '404-to-301')}>
				<PanelRow>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('WordPress URL guessing', '404-to-301')}
						help={__(
							'How aggressively to block WordPress’ auto-correction of unknown URLs. "Light" stops the closest-post guess; "Strict" also bypasses trailing-slash, case, and attachment fallback redirects.',
							'404-to-301',
						)}
						value={(() => {
							const raw = getSetting('disable_guessing', 'light')
							// Defend against stale boolean values that
							// may live on disk from earlier pre-release
							// builds: render them as the closest enum
							// equivalent rather than crashing the
							// SelectControl.
							if (raw === true) return 'strict'
							if (raw === false) return 'off'
							return ['off', 'light', 'strict'].includes(raw)
								? raw
								: 'light'
						})()}
						options={[
							{
								value: 'off',
								label: __(
									'Off — let WordPress guess (default WP behaviour)',
									'404-to-301',
								),
							},
							{
								value: 'light',
								label: __(
									'Light — block closest-post guessing only',
									'404-to-301',
								),
							},
							{
								value: 'strict',
								label: __(
									'Strict — bypass redirect_canonical() entirely',
									'404-to-301',
								),
							},
						]}
						onChange={(v) => setSetting('disable_guessing', v)}
					/>
				</PanelRow>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__(
							'Monitor post slug changes',
							'404-to-301',
						)}
						help={__(
							'Automatically create a redirect from the old URL to the new one when a post or page slug is renamed.',
							'404-to-301',
						)}
						checked={!!getSetting('monitor_post_slug', false)}
						onChange={(v) => setSetting('monitor_post_slug', v)}
					/>
				</PanelRow>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Mask IP addresses', '404-to-301')}
						help={__(
							'Drop visitor IPs before they hit the database. Useful for GDPR.',
							'404-to-301',
						)}
						checked={!!getSetting('mask_ip', false)}
						onChange={(v) => setSetting('mask_ip', v)}
					/>
				</PanelRow>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Track admin 404s', '404-to-301')}
						help={__(
							'Also process 404 requests that occur on the WordPress admin side. Off by default — most sites only care about front-end 404s.',
							'404-to-301',
						)}
						checked={!!getSetting('track_admin_404', false)}
						onChange={(v) => setSetting('track_admin_404', v)}
					/>
				</PanelRow>
			</PanelBody>

			{extra}

			{crossSell}
		</>
	)
}

export default General
