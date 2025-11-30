import { Disabled } from '@wordpress/components'

const SettingsPanel = ( { isEnabled, children } ) => {
	if ( isEnabled ) {
		return children
	}

	return <Disabled>{ children }</Disabled>
}

export default SettingsPanel
