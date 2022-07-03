import React from 'react'

import HeaderColumn from './../columns/header'
import HeaderSelectColumn from './../columns/header-select'

const HeaderRow = ({ columns, order, orderBy }) => {
	return (
		<tr>
			<HeaderSelectColumn />
			{columns.map((column) => (
				<HeaderColumn
					key={column.id}
					column={column}
					sorting={{
						order: order,
						orderBy: orderBy,
					}}
					orderChange={(orderBy, order) =>
						this.props.orderChange(orderBy, order)
					}
				/>
			))}
		</tr>
	)
}

export default HeaderRow
