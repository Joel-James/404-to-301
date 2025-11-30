import { __ } from '@wordpress/i18n'
import { BaseControl } from '@wordpress/components'
import { RepeatTable } from './../../components'
import useSettings from '../../hooks/settings'

const Exclusions = () => {
	const { getSetting, handleChange } = useSettings()

	return (
		<BaseControl
			__nextHasNoMarginBottom
			id="inspector-text-control-0"
			label={ __( 'Exclusions', '404-to-301' ) }
			help={ __(
				'Use this option to exclude a URL from being detected as 404 by the plugin. It will be wildcard checked using',
				'404-to-301'
			) }
		>
			<RepeatTable
				items={ getSetting( 'exclude_paths', [ 'wp-content' ] ) }
				onChange={ ( items ) => handleChange( 'exclude_paths', items ) }
			/>
		</BaseControl>
	)
}

export default Exclusions
