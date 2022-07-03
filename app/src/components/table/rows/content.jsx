import React from 'react'

import BodyColumn from './../columns/body'
import BodySelectColumn from './../columns/body-select'

export default class ContentRow extends React.Component {
	constructor(props) {
		super(props)

		this.state = {}
	}

	render() {
		let data = this.props.data

		return (
			<tr>
				<BodySelectColumn value={data.id} />
				{this.props.columns.map((column) => (
					<BodyColumn
						key={column.id}
						column={column}
						data={data}
						render={(data) => <h1>Hello {data.id}</h1>}
					/>
				))}
			</tr>
		)
	}
}
