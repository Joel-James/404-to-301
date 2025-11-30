import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { RadioControl } from '@wordpress/components'

const RedirectTarget = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<RadioControl
			label={ __( 'Redirect target' ) }
			help={ __(
				'From the target types, choose where you want to redirect the 404 errors to.',
				'4045-to-301'
			) }
			selected={ getSetting( 'redirect_target', 'link' ) }
			options={ [
				{ label: __( 'Page', '404-to-301' ), value: 'page' },
				{ label: __( 'Link', '404-to-301' ), value: 'link' },
			] }
			onChange={ ( selected ) =>
				handleChange( 'redirect_target', selected )
			}
		/>
	)
}

export default RedirectTarget
