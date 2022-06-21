import React from 'react'
import classNames from 'classnames'

const HeaderColumn = ({ column, sorting, orderChange }) => {
	let classes = column.classes ? column.classes : []
	// Primary class.
	if (column.isPrimary) {
		classes.push('column-primary')
	}
	// Add default common classes.
	classes.push('manage-column')
	classes.push('column-' + column.id)

	// Setup sorting classes.
	if (column.sortable) {
		if (sorting.orderBy === column.id) {
			classes.push('sorted')
			classes.push(sorting.order)
		} else {
			classes.push('sortable')
			classes.push('desc')
		}
	}

	return (
		<th scope="col" className={classNames(classes)}>
			{column.sortable ? (
				<a
					href="javascript:void(0)"
					onClick={() =>
						orderChange(
							column.id,
							sorting.order === 'desc' ? 'asc' : 'desc'
						)
					}
					onMouseDown={(ev) => ev.preventDefault()}
				>
					<span>{column.label}</span>
					<span className="sorting-indicator"></span>
				</a>
			) : (
				column.label
			)}
		</th>
	)
}

export default HeaderColumn
