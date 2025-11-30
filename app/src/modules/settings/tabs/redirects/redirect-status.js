import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ToggleControl } from '@wordpress/components'

const RedirectStatus = () => {
	const { getSetting, handleChange } = useSettings()
	// Check if redirect is enabled.
	const enabled = getSetting( 'redirect_enabled', false )

	return (
		<ToggleControl
			__nextHasNoMarginBottom
			checked={ enabled }
			label={ __( 'Enable redirects for 404 errors', '404-to-301' ) }
			help={ __(
				'Do you want to redirect the 404 errors to a new page or URL?',
				'404-to-301'
			) }
			onChange={ () => handleChange( 'redirect_enabled', ! enabled ) }
		/>
	)
}

export default RedirectStatus
