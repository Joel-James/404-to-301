import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ToggleControl } from '@wordpress/components'

const EmailStatus = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<ToggleControl
			__nextHasNoMarginBottom
			checked={ getSetting( 'email_enabled', false ) }
			label={ __(
				'Enable email notifications for 404 errors',
				'404-to-301'
			) }
			help={ __(
				'Do you want to receive and email notification for each 404 errors?',
				'404-to-301'
			) }
			onChange={ ( checked ) => handleChange( 'email_enabled', checked ) }
		/>
	)
}

export default EmailStatus
