import { __ } from '@wordpress/i18n'
import RedirectStatus from './redirect-status'
import RedirectType from './redirect-type'
import RedirectTarget from './redirect-target'
import RedirectPage from './redirect-page'
import RedirectLink from './redirect-link'
import useSettings from './../../hooks/settings'
import { SettingsPanel } from './../../components'
import { PanelBody, PanelRow } from '@wordpress/components'

const Redirects = () => {
	const { getSetting } = useSettings()
	const redirectTarget = getSetting( 'redirect_target', 'link' )
	const redirectEnabled = getSetting( 'redirect_enabled', false )

	return (
		<PanelBody title={ __( 'Redirects', '404-to-301' ) }>
			<PanelRow>
				<RedirectStatus />
			</PanelRow>
			<SettingsPanel isEnabled={ redirectEnabled }>
				<PanelRow>
					<RedirectType />
				</PanelRow>
				<PanelRow>
					<RedirectTarget />
				</PanelRow>
				<PanelRow>
					{ redirectTarget === 'link' ? (
						<RedirectLink />
					) : (
						<RedirectPage />
					) }
				</PanelRow>
			</SettingsPanel>
		</PanelBody>
	)
}

export default Redirects
