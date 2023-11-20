import React from 'react'
import ReactDOM from 'react-dom'
import '@/styles/settings.scss'
import Settings from '@/modules/settings'

// Get settings app container.
const container = document.getElementById('redirectpress-settings-app')

// Only if the app container found.
if (container !== null) {
	ReactDOM.render(<Settings />, container)
}
