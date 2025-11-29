import './styles/settings.scss'
import domReady from '@wordpress/dom-ready'
import { createRoot } from '@wordpress/element'
import SettingsPage from './modules/settings'

domReady( () => {
	const root = createRoot(
		document.getElementById( '404-to-301-settings-app' )
	)

	root.render( <SettingsPage /> )
} )
