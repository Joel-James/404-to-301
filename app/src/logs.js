require('./styles/settings.scss')

import React from 'react'
import ReactDOM from 'react-dom'
import Settings from './scripts/settings'

wp.domReady(function () {
	let settingsApp = document.getElementById('dd4t3-settings-app')

	if (settingsApp !== null) {
		ReactDOM.render(
			<Settings/>,
			document.getElementById('dd4t3-settings-app')
		);
	}
});