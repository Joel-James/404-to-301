import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ToggleControl } from '@wordpress/components'

const DisableGuessing = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<ToggleControl
			__nextHasNoMarginBottom
			checked={ getSetting( 'disable_guessing', false ) }
			label={ __( 'Stop WordPress from guessing URLs', '404-to-301' ) }
			help={ __(
				'WordPress will automatically correct a 404 URL if it is misspelled and very close to an existing link, before marking it as a 404 error.',
				'404-to-301'
			) }
			onChange={ ( checked ) =>
				handleChange( 'disable_guessing', checked )
			}
		/>
	)
}

export default DisableGuessing
