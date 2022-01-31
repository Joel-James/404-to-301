const {render} = wp.element

import './styles/redirects.scss'
import Redirects from './scripts/redirects'

// Only if the app container found.
if (document.getElementById('dd4t3-redirects-app') !== null) {
	render(
		<Redirects/>,
		document.getElementById('dd4t3-redirects-app')
	)
}