/**
 * Logs page entry — boots the React app on the mount point.
 */
import './styles/logs.scss'
import './utils/api-init'
import domReady from '@wordpress/dom-ready'
import { createRoot } from '@wordpress/element'
import LogsApp from './modules/logs'
import { AppShell } from './common'

domReady(() => {
	const el = document.getElementById('404-to-301-logs')

	if (el) {
		createRoot(el).render(
			<AppShell>
				<LogsApp />
			</AppShell>,
		)
	}
})
