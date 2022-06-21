import React from 'react'
import classNames from 'classnames'

const BodyColumn = ({ id, children, classes = null }) => {
	classes = classes ? classes : []
	// Add default common classes.
	classes.push('column-' + id)

	return <td className={classNames(classes)}>{children}</td>
}

export default BodyColumn
