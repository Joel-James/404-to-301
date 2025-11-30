import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { BaseControl, TextControl } from '@wordpress/components'

const RedirectLink = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<BaseControl
			__nextHasNoMarginBottom
			label={ __( 'Custom URL', '404-to-301' ) }
			help={ __(
				'Enter the email address where you want to get the email notification.',
				'404-to-301'
			) }
			id="duckdev-404-custom-url"
			className="duckdev-404-full-width"
		>
			<TextControl
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				value={ getSetting( 'redirect_link', '' ) }
				id="duckdev-404-custom-url"
				type="url"
				placeholder={ __( 'https://google.com' ) }
				onChange={ ( value ) => handleChange( 'redirect_link', value ) }
			/>
		</BaseControl>
	)
}

export default RedirectLink
