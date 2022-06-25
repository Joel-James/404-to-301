import React from 'react'

const Tooltip = ({ text, children }) => {
	return (
		<span className="dd4t3-logs-tooltip" data-tooltip={text}>
			{children}
		</span>
	)
}

export default Tooltip
