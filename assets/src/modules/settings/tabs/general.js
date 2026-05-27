import { __ } from '@wordpress/i18n'
import {
	PanelBody,
	PanelRow,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components'
import useSettings from '../../../hooks/use-settings'

/**
 * Convert the array<string> form used in storage to the textarea-friendly
 * newline-separated string, and back.
 */
const arrayToText = (arr) => (Array.isArray(arr) ? arr.join('\n') : '')
const textToArray = (str) =>
	String(str)
		.split(/[\r\n]+/)
		.map((s) => s.trim())
		.filter(Boolean)

const General = () => {
	const { getSetting, setSetting } = useSettings()

	return (
		<>
			<PanelBody title={__('Behaviour', '404-to-301')}>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__(
							'Disable WordPress URL guessing',
							'404-to-301',
						)}
						help={__(
							'Stop WordPress from auto-correcting incorrect URLs to the closest matching post.',
							'404-to-301',
						)}
						checked={!!getSetting('disable_guessing', true)}
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
			</PanelBody>

			<PanelBody title={__('Exclude paths', '404-to-301')}>
				<PanelRow>
					<TextareaControl
						__nextHasNoMarginBottom
						label={__('Paths to ignore', '404-to-301')}
						help={__(
							'One path per line. Any 404 whose URL contains one of these substrings is skipped — no log, no redirect, no email.',
							'404-to-301',
						)}
						value={arrayToText(getSetting('exclude_paths', []))}
						onChange={(v) =>
							setSetting('exclude_paths', textToArray(v))
						}
					/>
				</PanelRow>
			</PanelBody>
		</>
	)
}

export default General
