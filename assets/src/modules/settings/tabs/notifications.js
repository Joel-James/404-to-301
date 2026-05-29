import { __ } from '@wordpress/i18n'
import {
	Notice,
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl,
	__experimentalInputControl as InputControl,
	__experimentalInputControlPrefixWrapper as InputControlPrefixWrapper,
} from '@wordpress/components'
import { Icon, envelope } from '@wordpress/icons'
import useSettings from '../../../hooks/use-settings'

const Notifications = () => {
	const { getSetting, setSetting } = useSettings()

	const enabled = !!getSetting('email_enabled', false)

	return (
		<PanelBody title={__('Email notifications', '404-to-301')}>
			<PanelRow>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Notify by email on 404 errors', '404-to-301')}
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
				<InputControl
					__next40pxDefaultSize
					type="email"
					label={__('Recipient email', '404-to-301')}
					help={__(
						'Where to send the notifications.',
						'404-to-301',
					)}
					prefix={
						<InputControlPrefixWrapper>
							<Icon icon={envelope} size={20} />
						</InputControlPrefixWrapper>
					}
					value={getSetting('email_recipient', '')}
					onChange={(v) => setSetting('email_recipient', v ?? '')}
				/>
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
		</PanelBody>
	)
}

export default Notifications
