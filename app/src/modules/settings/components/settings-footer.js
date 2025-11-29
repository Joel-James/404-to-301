import { __ } from '@wordpress/i18n'
import useSettings from '../hooks/settings'
import { Button } from '@wordpress/components'

const SettingsFooter = () => {
	const { isSaving, hasLoaded, saveSettings } = useSettings()

	return (
		<div className="duckdev-404-settings-footer">
			{ hasLoaded && (
				<Button
					disabled={ isSaving }
					isBusy={ isSaving }
					variant="primary"
					icon={ isSaving ? null : 'yes' }
					onClick={ () => saveSettings() }
				>
					{ isSaving
						? __( 'Saving Changes..', '404-to-301' )
						: __( 'Save Changes', '404-to-301' ) }
				</Button>
			) }
		</div>
	)
}

export default SettingsFooter
