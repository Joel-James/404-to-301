import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ToggleControl } from '@wordpress/components'

const DisableIp = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<ToggleControl
			__nextHasNoMarginBottom
			checked={ getSetting( 'disable_ip', false ) }
			label={ __( "Do not log visitor's IP address", '404-to-301' ) }
			help={ __(
				"To respect visitor's privacy and comply with GDPR policies, you may disable a few functionalities of the plugin.",
				'404-to-301'
			) }
			onChange={ ( checked ) => handleChange( 'disable_ip', checked ) }
		/>
	)
}

export default DisableIp
