import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { TextControl } from '@wordpress/components'

const EmailRecipient = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<TextControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={ __( 'Recipient email', '404-to-301' ) }
			help={ __(
				'Enter the email address where you want to get the email notification.',
				'404-to-301'
			) }
			type="email"
			value={ getSetting( 'email_recipient', '' ) }
			onChange={ ( value ) => handleChange( 'email_recipient', value ) }
		/>
	)
}

export default EmailRecipient
