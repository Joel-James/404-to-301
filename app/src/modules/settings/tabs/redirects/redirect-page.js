/* global duckdev404 */
import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ComboboxControl } from '@wordpress/components'

const RedirectPage = () => {
	const { getSetting, handleChange } = useSettings()

	// Setup page list.
	const pages = []
	if ( duckdev404.pages ) {
		Object.keys( duckdev404.pages ).forEach( ( id ) => {
			pages.push( {
				label: duckdev404.pages[ id ],
				value: id,
			} )
		} )
	}

	return (
		<ComboboxControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={ __( 'Select page', '404-to-301' ) }
			help={ __(
				'Enter the email address where you want to get the email notification.',
				'404-to-301'
			) }
			value={ getSetting( 'redirect_page', '' ) }
			onChange={ ( selected ) =>
				handleChange( 'redirect_page', selected )
			}
			options={ pages }
		/>
	)
}

export default RedirectPage
