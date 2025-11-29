import { __ } from '@wordpress/i18n'
import { PanelRow, ToggleControl } from '@wordpress/components'
import useSettings from '../../hooks/settings'

const RedirectStatus = () => {
	const { settings, handleChange } = useSettings();

	const enabled = settings?.redirect_enabled ?? false;

	return (
		<PanelRow>
			<ToggleControl
				checked={enabled}
				label={__(
					'Enable redirects for 404 errors',
					'404-to-301'
				)}
				help={__(
					'Do you want to redirect the 404 errors to a new page or URL?',
					'404-to-301'
				)}
				onChange={() => handleChange('redirect_enabled', !enabled)}
			/>
		</PanelRow>
	)
}

export default RedirectStatus