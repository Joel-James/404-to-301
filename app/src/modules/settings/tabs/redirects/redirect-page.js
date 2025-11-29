/* global duckdev404 */
import { __ } from '@wordpress/i18n'
import { ComboboxControl, PanelRow } from '@wordpress/components'
import useSettings from '../../hooks/settings'

const RedirectPage = () => {
	const { settings, handleChange } = useSettings();
	const redirectPage = settings.redirect_page ?? '';
	let pages = []
	// Setup page list.
	if (duckdev404.pages) {
		Object.keys(duckdev404.pages).forEach((id) => {
			pages.push({
				label: duckdev404.pages[id],
				value: id,
			})
		})
	}

	return (
		<PanelRow>
			<ComboboxControl
				label={__('Select page', '404-to-301')}
				help={__(
					'Enter the email address where you want to get the email notification.',
					'404-to-301'
				)}
				value={redirectPage}
				onChange={(selected) => handleChange('redirect_page', selected)}
				options={pages}
			/>
		</PanelRow>
	)
}

export default RedirectPage