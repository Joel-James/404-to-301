import React from 'react'
import ReactDOM from 'react-dom'

import '@/styles/logs.scss'
import Logs from './modules/logs'

// Get logs app container.
const container = document.getElementById('redirectpress-logs-app')

// Only if the app container found.
if (container !== null) {
	ReactDOM.render(<Logs />, container)
}
