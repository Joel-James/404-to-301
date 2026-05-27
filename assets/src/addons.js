/**
 * Addons page entry — boots the React app on the mount point.
 */
import './styles/addons.scss'
import './utils/api-init'
import domReady from '@wordpress/dom-ready'
import { createRoot } from '@wordpress/element'
import AddonsApp from './modules/addons'
import { AppShell } from './common'

domReady(() => {
	const el = document.getElementById('404-to-301-addons')

	if (el) {
		createRoot(el).render(
			<AppShell>
				<AddonsApp />
			</AppShell>,
		)
	}
})
