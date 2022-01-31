const {render} = wp.element

import './styles/style.scss'
import Settings from './scripts/settings'

// Only if the app container found.
if (document.getElementById('dd4t3-settings-app') !== null) {
	render(
		<Settings/>,
		document.getElementById('dd4t3-settings-app')
	)
}