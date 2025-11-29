import { __ } from '@wordpress/i18n'
import RedirectStatus from './redirect-status'
import RedirectType from './redirect-type'
import RedirectTarget from './redirect-target'
import RedirectPage from './redirect-page'
import RedirectLink from './redirect-link'
import { PanelBody } from '@wordpress/components'
import useSettings from './../../hooks/settings'

const Redirects = () => {
	const { settings } = useSettings()
	const redirectTarget = settings.redirect_target ?? 'link'

	return (
		<PanelBody title={ __( 'Redirects', '404-to-301' ) }>
			<RedirectStatus />
			<RedirectType />
			<RedirectTarget />
			{ redirectTarget === 'link' ? <RedirectLink /> : <RedirectPage /> }
		</PanelBody>
	)
}

export default Redirects
