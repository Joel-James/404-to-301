import { __ } from '@wordpress/i18n'
import LogStatus from './log-status'
import SkipDuplicates from './skip-duplicates'
import useSettings from '../../hooks/settings'
import { SettingsPanel } from './../../components'
import { PanelBody, PanelRow } from '@wordpress/components'

const Logs = () => {
	const { getSetting } = useSettings()
	const logsEnabled = getSetting( 'logs_enabled', false )

	return (
		<PanelBody title={ __( 'Logs', '404-to-301' ) }>
			<PanelRow>
				<LogStatus />
			</PanelRow>
			<SettingsPanel isEnabled={ logsEnabled }>
				<PanelRow>
					<SkipDuplicates />
				</PanelRow>
			</SettingsPanel>
		</PanelBody>
	)
}

export default Logs
