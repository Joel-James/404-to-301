import { __ } from '@wordpress/i18n'
import { Notices } from './../../components'
import { General, Logs, Notifications, Redirects } from './tabs'
import useSettings from './hooks/settings'
import { Placeholder, Spinner } from '@wordpress/components'
import { useState } from '@wordpress/element'
import {
	SettingsBody,
	SettingsFooter,
	SettingsHeader,
	TabContent,
	TabNav,
} from './components'

const SettingsPage = () => {
	const { hasLoaded } = useSettings()
	const [ currentTab, setCurrentTab ] = useState( 'redirects' )

	return (
		<>
			<SettingsHeader
				title={ __( '404 to 301 - Settings', '404-to-301' ) }
			>
				<TabNav
					current={ currentTab }
					navs={ {
						redirects: __( 'Redirects', '404-to-301' ),
						logs: __( 'Logs', '404-to-301' ),
						notifications: __( 'Notifications', '404-to-301' ),
						general: __( 'General', '404-to-301' ),
					} }
					onChange={ ( tab ) => setCurrentTab( tab ) }
				/>
			</SettingsHeader>
			<SettingsBody>
				{ ! hasLoaded ? (
					<Placeholder>
						<Spinner />
					</Placeholder>
				) : (
					<>
						<Notices />
						<TabContent
							currentTab={ currentTab }
							tabs={ {
								redirects: Redirects,
								logs: Logs,
								notifications: Notifications,
								general: General,
							} }
						/>
						<SettingsFooter />
					</>
				) }
			</SettingsBody>
		</>
	)
}

export default SettingsPage
