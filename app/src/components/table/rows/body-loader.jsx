import React from 'react'
import { Spinner, __experimentalHStack as HStack } from '@wordpress/components'
import BodySelectColumn from './../columns/body-select'

export default class BodyLoaderRow extends React.Component {
	constructor(props) {
		super(props)

		this.state = {}
	}

	render() {
		return (
			<tr>
				<BodySelectColumn value="" disabled={true} />
				<td
					className="column-loading"
					colSpan={this.props.columns.length}
				>
					<HStack alignment="center">
						<Spinner />
					</HStack>
				</td>
			</tr>
		)
	}
}
