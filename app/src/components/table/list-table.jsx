import React from 'react'
import HeaderRow from './rows/header'
import BodyLoaderRow from './rows/body-loader'

export default class ListTable extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			order: 'desc',
			orderBy: 'title',
		}
	}

	render() {
		return (
			<table className="wp-list-table widefat fixed striped posts">
				<thead>
					<HeaderRow
						columns={this.props.columns}
						order={this.state.order}
						orderBy={this.state.orderBy}
						orderChange={(orderBy, order) =>
							this.setState({
								order: order,
								orderBy: orderBy,
							})
						}
					/>
				</thead>

				<tbody id="the-list">
					{this.props.loading ? (
						<BodyLoaderRow columns={this.props.columns} />
					) : (
						this.props.children
					)}
				</tbody>
				<tfoot>
					<HeaderRow
						columns={this.props.columns}
						order={this.state.order}
						orderBy={this.state.orderBy}
						orderChange={(orderBy, order) =>
							this.setState({
								order: order,
								orderBy: orderBy,
							})
						}
					/>
				</tfoot>
			</table>
		)
	}
}
