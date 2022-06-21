/* global wp */
import React from 'react'
const { __ } = wp.i18n

import HeaderColumn from './../columns/header-column'
import HeaderSelectColumn from './../columns/header-select'

export default class HeaderRow extends React.Component {
	constructor(props) {
		super(props)

		this.state = {}
	}

	render() {
		return (
			<tr>
				<HeaderSelectColumn />
				{this.props.columns.map((column) => (
					<HeaderColumn
						key={column.id}
						column={column}
						sorting={{
							order: this.props.order,
							orderBy: this.props.orderBy,
						}}
						orderChange={(orderBy, order) =>
							this.props.orderChange(orderBy, order)
						}
					/>
				))}
			</tr>
		)
	}
}
