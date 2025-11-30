import { __ } from '@wordpress/i18n'
import useSettings from '../../hooks/settings'
import { ToggleControl } from '@wordpress/components'

const SkipDuplicates = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<ToggleControl
			__nextHasNoMarginBottom
			label={ __( 'Skip duplicate entries from the logs', '404-to-301' ) }
			help={ __(
				'You may get 100s of visits to an old or non-existing link on your website. This can create 100s of copies of the same 404 link. If you enable this, the duplicates will be skipped without affecting the redirects. This will be helpful to keep your database light.',
				'404-to-301'
			) }
			checked={ getSetting( 'logs_skip_duplicates', false ) }
			onChange={ ( checked ) =>
				handleChange( 'logs_skip_duplicates', checked )
			}
		/>
	)
}

export default SkipDuplicates
