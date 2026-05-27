import { __ } from '@wordpress/i18n'
import { Button } from '@wordpress/components'
import useSettings from '../hooks/use-settings'

/**
 * Sticky footer with the save button.
 */
const Footer = () => {
	const { isSaving, isDirty, saveSettings } = useSettings()

	return (
		<div className="d404-footer">
			<Button
				variant="primary"
				icon={isSaving ? null : 'yes'}
				isBusy={isSaving}
				disabled={isSaving || !isDirty}
				onClick={saveSettings}
			>
				{isSaving
					? __('Saving…', '404-to-301')
					: __('Save Changes', '404-to-301')}
			</Button>
		</div>
	)
}

export default Footer
