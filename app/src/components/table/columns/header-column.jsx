import React from 'react'
import classNames from 'classnames'

const HeaderColumn = (props) => {
	let classes = props.classes ? props.classes : []
	// Add default common classes.
	classes.push('manage-column')
	classes.push('column-' + props.id)

	return (
		<th scope="col" id={props.id} className={classNames(classes)}>
			{props.label}
		</th>
	)
}

export default HeaderColumn
