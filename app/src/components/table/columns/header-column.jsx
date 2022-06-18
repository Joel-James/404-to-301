import React from 'react'
import classNames from 'classnames'

const HeaderColumn = (props) => {
	let classes = props.classes ? props.classes : []
	// Add default common classes.
	classes.push('manage-column')
	classes.push('column-' + props.id)

	if (props.sortable) {
		classes.push('sortable')
		classes.push('desc')
	}

	return (
		<th scope="col" id={props.id} className={classNames(classes)}>
			<a href="#">
				<span>{props.label}</span>
				{props.sortable && <span className="sorting-indicator"></span>}
			</a>
		</th>
	)
}

export default HeaderColumn
