import React from 'react'

const Tooltip = ({ text, children }) => {
	return (
		<span className="redirectpress-logs-tooltip" data-tooltip={text}>
			{children}
		</span>
	)
}

export default Tooltip
