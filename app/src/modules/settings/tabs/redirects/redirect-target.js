/* global duckdev404 */
import { __ } from '@wordpress/i18n'
import { PanelRow, RadioControl } from '@wordpress/components'
import useSettings from '../../hooks/settings'

const RedirectTarget = () => {
	const { settings, handleChange } = useSettings();
	const redirectTarget = settings.redirect_target ?? 'link';

	return (
		<PanelRow>
			<RadioControl
				label={__('Redirect target')}
				help={__(
					'From the target types, choose where you want to redirect the 404 errors to.',
					'4045-to-301'
				)}
				selected={redirectTarget}
				options={[
					{ label: __('Page', '404-to-301'), value: 'page' },
					{ label: __('Link', '404-to-301'), value: 'link' },
				]}
				onChange={(selected) => handleChange('redirect_target', selected)}
			/>
		</PanelRow>
	)
}

export default RedirectTarget