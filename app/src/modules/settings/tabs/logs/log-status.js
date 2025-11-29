import { __ } from '@wordpress/i18n'
import { PanelRow, ToggleControl } from '@wordpress/components'
import useSettings from '../../hooks/settings'

const LogStatus = () => {
	const { settings, handleChange } = useSettings();

	const enabled = settings?.logs_enabled ?? false;

	return (
		<PanelRow>
			<ToggleControl
				checked={enabled}
				label={__('Enable logs for 404 errors', '404-to-301')}
				help={__(
					'This will be helpful for you to keep track of broken links to your website. You can also setup individual redirects for each 404s from the logs page.',
					'404-to-301'
				)}
				onChange={() => handleChange('redirect_enabled', !enabled)}
			/>
		</PanelRow>
	)
}

export default LogStatus