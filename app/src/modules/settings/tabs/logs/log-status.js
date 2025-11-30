import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ToggleControl } from '@wordpress/components'

const LogStatus = () => {
	const { getSetting, handleChange } = useSettings()
	const enabled = getSetting( 'logs_enabled', false )

	return (
		<ToggleControl
			__nextHasNoMarginBottom
			checked={ enabled }
			label={ __( 'Enable logs for 404 errors', '404-to-301' ) }
			help={ __(
				'This will be helpful for you to keep track of broken links to your website. You can also setup individual redirects for each 404s from the logs page.',
				'404-to-301'
			) }
			onChange={ () => handleChange( 'logs_enabled', ! enabled ) }
		/>
	)
}

export default LogStatus
