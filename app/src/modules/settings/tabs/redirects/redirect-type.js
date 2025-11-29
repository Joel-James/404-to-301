/* global duckdev404 */
import { __ } from '@wordpress/i18n'
import { PanelRow, SelectControl } from '@wordpress/components'
import useSettings from '../../hooks/settings'

const RedirectType = () => {
	const { settings, handleChange } = useSettings();
	const redirectType = settings.redirect_type ?? 301;
	let types = []
	Object.keys(duckdev404.types).forEach((type) => {
		types.push({
			value: type,
			label: duckdev404.types[type],
		})
	})

	return (
		<PanelRow>
			<SelectControl
				__nextHasNoMarginBottom
				label={__('Redirect type', '404-to-301')}
				help={__(
					'The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.',
					'404-to-301'
				)}
				onChange={(selected) => handleChange('redirect_type', selected)}
				options={types}
				value={redirectType}
			/>
		</PanelRow>
	)
}

export default RedirectType