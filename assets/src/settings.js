/**
 * Settings page entry — boots the React app on the mount point.
 */
import './styles/settings.scss'
import './utils/api-init'
import domReady from '@wordpress/dom-ready'
import { createRoot } from '@wordpress/element'
import SettingsApp from './modules/settings'
import { AppShell } from './common'

domReady(() => {
	const el = document.getElementById('404-to-301-settings')

	if (el) {
		createRoot(el).render(
			<AppShell>
				<SettingsApp />
			</AppShell>,
		)
	}
})
