import React from 'react'
import ReactDOM from 'react-dom'
import './styles/redirects.scss'
import Redirects from './scripts/redirects'

// Get redirects app container.
const container = document.getElementById('dd4t3-redirects-app')

// Only if the app container found.
if (container !== null) {
	ReactDOM.render(<Redirects />, container)
}
