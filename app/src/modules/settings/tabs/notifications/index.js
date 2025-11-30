import { __ } from '@wordpress/i18n'
import EmailStatus from './email-status'
import EmailRecipient from './email-recipient'
import useSettings from '../../hooks/settings'
import { SettingsPanel } from './../../components'
import { PanelBody, PanelRow } from '@wordpress/components'

const Notifications = () => {
	const { getSetting } = useSettings()
	const emailEnabled = getSetting( 'email_enabled', false )

	return (
		<PanelBody title={ __( 'Notifications', '404-to-301' ) }>
			<PanelRow>
				<EmailStatus />
			</PanelRow>
			<SettingsPanel isEnabled={ emailEnabled }>
				<PanelRow>
					<EmailRecipient />
				</PanelRow>
			</SettingsPanel>
		</PanelBody>
	)
}

export default Notifications
