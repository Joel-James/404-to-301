import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ToggleControl } from '@wordpress/components'

const MonitorChanges = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<ToggleControl
			__nextHasNoMarginBottom
			checked={ getSetting( 'monitor_changes', false ) }
			label={ __(
				'Monitor permalink changes and create redirects',
				'404-to-301'
			) }
			help={ __(
				'New 404 errors can be created when you change an existing page/post permalink to a new one. Instead of waiting for someone to visit and create a 404 error, ww can create a redirect ourself to the new permalink.',
				'404-to-301'
			) }
			onChange={ ( checked ) =>
				handleChange( 'monitor_changes', checked )
			}
		/>
	)
}

export default MonitorChanges
