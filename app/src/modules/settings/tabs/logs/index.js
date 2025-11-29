import { __ } from '@wordpress/i18n'
import LogStatus from './log-status'
import { PanelBody } from '@wordpress/components'

const Logs = () => {
	return (
		<PanelBody title={__('Logs', '404-to-301')}>
			<LogStatus/>
		</PanelBody>
	)
}

export default Logs