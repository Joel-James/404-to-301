const {Component} = wp.element
const {__} = wp.i18n
const {
	Button,
	TextControl,
} = wp.components

export default class RepeatTable extends Component {
	constructor(props) {
		super(props);
		this.state = {
			items: this.props.items
		}
	}

	/**
	 * Add new row to the table.
	 *
	 * @since 4.0.0
	 */
	addRow() {
		let items = this.state.items
		// Add new item.
		items.push('')
		// Push new row.
		this.setState({
			items: items
		})
	}

	/**
	 * Add new row to the table.
	 *
	 * @since 4.0.0
	 */
	updateRow(value, index) {
		let items = this.state.items
		// Add new item.
		items[index] = value
		// Push new row.
		this.setState({
			items: items
		})
	}

	/**
	 * Delete one row using it's index.
	 *
	 * @param {int} index Index of item.
	 *
	 * @since 4.0.0
	 */
	deleteRow(index) {
		let items = this.state.items
		// Remove one item from array.
		items.splice(index, 1)
		this.setState({
			items: items
		})
	}

	render() {
		return (
			<table className="duckdev-table">
				<tbody>
				{this.state.items.map((item, index) =>
					<tr key={index}>
						<td>
							<TextControl
								placeholder="wp-content/"
								value={item}
								onChange={(value) => this.updateRow(value, index)}
							/>
						</td>
						<td>
							<Button
								variant="tertiary"
								isSmall={true}
								icon="trash"
								onClick={() => this.deleteRow(index)}
							>
							</Button>
						</td>
					</tr>
				)}
				</tbody>
				<tfoot>
				<tr>
					<td colSpan="2">
						<Button
							variant="secondary"
							isSmall={true}
							icon="plus"
							onClick={() => this.addRow()}
						>
							{__('Add', '404-to-301')}
						</Button>
					</td>
				</tr>
				</tfoot>
			</table>
		);
	}
}