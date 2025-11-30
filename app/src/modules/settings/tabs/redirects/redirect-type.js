/* global duckdev404 */
import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { SelectControl } from '@wordpress/components'

const RedirectType = () => {
	const { getSetting, handleChange } = useSettings()

	// Format redirect types.
	const types = []
	Object.keys( duckdev404.types ).forEach( ( type ) => {
		types.push( {
			value: type,
			label: duckdev404.types[ type ],
		} )
	} )

	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={ __( 'Redirect type', '404-to-301' ) }
			help={ __(
				'The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.',
				'404-to-301'
			) }
			onChange={ ( selected ) =>
				handleChange( 'redirect_type', selected )
			}
			options={ types }
			value={ getSetting( 'redirect_type', 301 ) }
		/>
	)
}

export default RedirectType
