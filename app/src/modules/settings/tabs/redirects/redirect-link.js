/* global duckdev404 */
import { __ } from '@wordpress/i18n'
import { PanelRow, BaseControl, TextControl } from '@wordpress/components'
import useSettings from '../../hooks/settings'

const RedirectLink = () => {
	const { settings, handleChange } = useSettings();
	const redirectLink = settings.redirect_link ?? '';

	return (
		<PanelRow>
			<BaseControl
				label={__('Custom URL', '404-to-301')}
				help={__(
					'Enter the email address where you want to get the email notification.',
					'404-to-301'
				)}
				id="duckdev-404-custom-url"
				className="duckdev-404-full-width"
			>
				<TextControl
					value={redirectLink}
					id="duckdev-404-custom-url"
					type="url"
					placeholder={__('https://google.com')}
					onChange={(value) => handleChange('redirect_link', value)}
				/>
			</BaseControl>
		</PanelRow>
	)
}

export default RedirectLink