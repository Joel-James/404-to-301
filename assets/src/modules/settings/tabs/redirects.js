import { __ } from '@wordpress/i18n'
import {
	Notice,
	PanelBody,
	PanelRow,
	SelectControl,
	ToggleControl,
	__experimentalInputControl as InputControl,
	__experimentalInputControlPrefixWrapper as InputControlPrefixWrapper,
} from '@wordpress/components'
import { Icon, link } from '@wordpress/icons'
import { applyFilters } from '@wordpress/hooks'
import useSettings from '../../../hooks/use-settings'
import { redirectingTypeOptions, PageSelect } from '../../../common'

const RedirectsTab = () => {
	const { getSetting, setSetting } = useSettings()

	const enabled = !!getSetting('redirect_enabled', true)
	const target = getSetting('redirect_target', 'link')

	/*
	 * Addon extension point. Addons hook into
	 * `d404.settings.redirects.fields` via `@wordpress/hooks` and return
	 * one or more React nodes rendered at the end of the Redirect
	 * PanelBody. The filter receives the `getSetting` / `setSetting`
	 * accessors so the injected controls read and write through the
	 * same hook the built-in fields use.
	 *
	 * Note: the hook name must start with a letter — `@wordpress/hooks`
	 * rejects names that lead with a digit, so we use the `d404` prefix
	 * here instead of `404_to_301`.
	 */
	const extra = applyFilters('d404.settings.redirects.fields', null, {
		getSetting,
		setSetting,
	})

	/*
	 * Cross-sell slot. No default promo today, but the filter exists so
	 * addons can inject (or replace) one without a future parent-side
	 * code change.
	 */
	const crossSell = applyFilters('d404.settings.redirects.cross_sell', null)

	return (
		<PanelBody title={__('404 Redirects', '404-to-301')}>
			<PanelRow>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Enable redirect', '404-to-301')}
					help={__(
						'When on, 404 URLs without a specific custom redirect fall back to the destination configured here.',
						'404-to-301',
					)}
					checked={enabled}
					onChange={(v) => setSetting('redirect_enabled', v)}
				/>
			</PanelRow>

			{!enabled && (
				<PanelRow>
					<Notice status="info" isDismissible={false}>
						{__(
							'The redirect is off. Per-URL redirects from the Redirects page still fire.',
							'404-to-301',
						)}
					</Notice>
				</PanelRow>
			)}

			<PanelRow>
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Redirect type', '404-to-301')}
					help={__(
						'HTTP status code used when this fallback redirect fires.',
						'404-to-301',
					)}
					value={getSetting('redirect_type', '301')}
					// Non-terminal codes only — the fallback always points
					// at a destination. Sourced from the shared PHP catalogue
					// so it matches the `redirect_type` setting's REST enum.
					options={redirectingTypeOptions}
					onChange={(v) => setSetting('redirect_type', v)}
				/>
			</PanelRow>

			<PanelRow>
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Redirect to', '404-to-301')}
					help={__(
						'Where 404 requests land when no custom redirect matches.',
						'404-to-301',
					)}
					value={target}
					options={[
						{
							label: __('A custom URL', '404-to-301'),
							value: 'link',
						},
						{
							label: __('An existing page', '404-to-301'),
							value: 'page',
						},
						{
							label: __('No redirect', '404-to-301'),
							value: 'none',
						},
					]}
					onChange={(v) => setSetting('redirect_target', v)}
				/>
			</PanelRow>

			{target === 'link' && (
				<PanelRow>
					<InputControl
						__next40pxDefaultSize
						type="url"
						label={__('Destination URL', '404-to-301')}
						help={__(
							'Absolute URL (including https://).',
							'404-to-301',
						)}
						prefix={
							<InputControlPrefixWrapper>
								<Icon icon={link} size={20} />
							</InputControlPrefixWrapper>
						}
						value={getSetting('redirect_link', '')}
						onChange={(v) => setSetting('redirect_link', v ?? '')}
					/>
				</PanelRow>
			)}

			{target === 'page' && (
				<PanelRow>
					<PageSelect
						label={__('Destination page', '404-to-301')}
						help={__(
							'Search and pick the page that should serve as the 404 destination.',
							'404-to-301',
						)}
						value={getSetting('redirect_page', 0)}
						onChange={(v) => setSetting('redirect_page', v)}
					/>
				</PanelRow>
			)}

			{extra}
			{crossSell}
		</PanelBody>
	)
}

export default RedirectsTab
