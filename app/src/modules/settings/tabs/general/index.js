import { __ } from '@wordpress/i18n'
import DisableIp from './disable-ip'
import Exclusions from './exclusions'
import MonitorChanges from './monitor-changes'
import DisableGuessing from './disable-guessing'
import { PanelBody, PanelRow } from '@wordpress/components'

const General = () => {
	return (
		<PanelBody title={ __( 'Notifications', '404-to-301' ) }>
			<PanelRow>
				<DisableGuessing />
			</PanelRow>
			<PanelRow>
				<MonitorChanges />
			</PanelRow>
			<PanelRow>
				<DisableIp />
			</PanelRow>
			<PanelRow>
				<Exclusions />
			</PanelRow>
		</PanelBody>
	)
}

export default General
