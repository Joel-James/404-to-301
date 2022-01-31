const {render} = wp.element

import './styles/logs.scss'
import Logs from './scripts/logs'

// Only if the app container found.
if (document.getElementById('dd4t3-logs-app') !== null) {
	render(
		<Logs/>,
		document.getElementById('dd4t3-logs-app')
	)
}