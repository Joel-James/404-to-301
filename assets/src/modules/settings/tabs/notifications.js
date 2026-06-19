import { __ } from '@wordpress/i18n'
import {
	BaseControl,
	FormTokenField,
	Notice,
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl,
} from '@wordpress/components'
import { applyFilters } from '@wordpress/hooks'
import useSettings from '../../../hooks/use-settings'

// RFC 5322-light email check — good enough for client-side feedback;
// the server re-validates with `is_email()` before save.
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

// Normalise whatever shape the settings store hands us (legacy scalar
// or new array) into a string[] the FormTokenField can render.
const toRecipients = (value) => {
	if (Array.isArray(value))
		return value.map((v) => String(v).trim()).filter(Boolean)
	if (typeof value === 'string') {
		return value
			.split(',')
			.map((s) => s.trim())
			.filter(Boolean)
	}
	return []
}

const Notifications = () => {
	const { getSetting, setSetting } = useSettings()

	const enabled = !!getSetting('email_enabled', false)

	/*
	 * Addon extension point. Addons (eg. Email Reports) hook into
	 * `d404.settings.notifications.fields` via `@wordpress/hooks` and
	 * return one or more React nodes that are rendered at the end of
	 * the Email notifications PanelBody. The filter receives the
	 * `getSetting` / `setSetting` accessors so the injected controls
	 * read and write through the same hook the built-in fields use.
	 *
	 * Note: the hook name must start with a letter — `@wordpress/hooks`
	 * rejects names that lead with a digit, so we use the `d404`
	 * prefix here instead of `404_to_301`.
	 */
	const extra = applyFilters('d404.settings.notifications.fields', null, {
		getSetting,
		setSetting,
	})

	/*
	 * Sibling-panel slot. Unlike `…fields` (which injects rows *inside*
	 * the Email-notifications box above), this renders *below* it as a
	 * separate PanelBody sibling — for notification-adjacent settings
	 * that are NOT governed by the global email toggle. The Email
	 * Reports addon adds its own box here. Receives the same accessors
	 * so injected panels read/write through the same hook.
	 */
	const afterPanels = applyFilters(
		'd404.settings.notifications.after',
		null,
		{
			getSetting,
			setSetting,
		},
	)

	/*
	 * Cross-sell slot. No default promo today, but the filter exists so
	 * addons (eg. Email Reports) can inject — or replace — a promo
	 * without a future parent-side code change.
	 */
	const crossSell = applyFilters(
		'd404.settings.notifications.cross_sell',
		null,
	)

	return (
		<>
			<PanelBody title={__('Email Notifications', '404-to-301')}>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__(
							'Notify by email on 404 errors',
							'404-to-301',
						)}
						help={__(
							'Send an email when a 404 fires. Honours the threshold below so the inbox does not get flooded on busy sites.',
							'404-to-301',
						)}
						checked={enabled}
						onChange={(v) => setSetting('email_enabled', v)}
					/>
				</PanelRow>

				{!enabled && (
					<PanelRow>
						<Notice status="info" isDismissible={false}>
							{__(
								'Notifications are off — the threshold and recipient below are ignored until you enable them.',
								'404-to-301',
							)}
						</Notice>
					</PanelRow>
				)}

				<PanelRow>
					<BaseControl
						__nextHasNoMarginBottom
						id="d404-email-recipients"
						label={__('Recipient emails', '404-to-301')}
						help={__(
							'Press Enter or comma to add an address. Add as many recipients as you need.',
							'404-to-301',
						)}
					>
						<FormTokenField
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							__experimentalExpandOnFocus
							__experimentalValidateInput={(v) =>
								EMAIL_RE.test(String(v).trim())
							}
							label={null}
							value={toRecipients(
								getSetting('email_recipient', []),
							)}
							onChange={(tokens) =>
								setSetting(
									'email_recipient',
									toRecipients(tokens),
								)
							}
							tokenizeOnSpace
							tokenizeOnBlur
							placeholder={__('name@example.com', '404-to-301')}
						/>
					</BaseControl>
				</PanelRow>

				<PanelRow>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						type="number"
						min={1}
						label={__('Hits threshold', '404-to-301')}
						help={__(
							'Only send an email once a URL has racked up at least this many 404 hits.',
							'404-to-301',
						)}
						value={getSetting('email_threshold', 1)}
						onChange={(v) =>
							setSetting(
								'email_threshold',
								Math.max(1, parseInt(v, 10) || 1),
							)
						}
					/>
				</PanelRow>
				{extra}
				{crossSell}
			</PanelBody>

			{afterPanels}
		</>
	)
}

export default Notifications
