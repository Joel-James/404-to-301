/* global wp */
import React from 'react'
import { Button, TextControl } from '@wordpress/components'

const { __ } = wp.i18n

export default class RepeatTable extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			items: this.props.items,
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
			items: items,
		})
	}

	/**
	 * Update an item in the table.
	 *
	 * @since 4.0.0
	 */
	updateRow(value, index) {
		let items = this.state.items
		// Add new item.
		items[index] = value
		// Push new row.
		this.setState({
			items: items,
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
			items: items,
		})
	}

	/**
	 * Check if last item of the array is empty.
	 *
	 * @since 4.0.0
	 *
	 * @return {boolean}
	 */
	isLastItemEmpty() {
		let items = this.state.items
		return '' === items.slice(-1)[0]
	}

	/**
	 * On state change update items in settings.
	 *
	 * @param {object} prevProps Previous props.
	 * @param {object} prevState Previous state.
	 *
	 * @since 4.0.0
	 */
	componentDidUpdate(prevProps, prevState) {
		if (this.state.items !== prevState.items) {
			this.props.onChange(this.state.items)
		}
	}

	render() {
		return (
			<table className="dd4t3-settings-repeat-table">
				<tbody>
				{this.state.items.map((item, index) => (
					<tr key={index}>
						<td>
							<TextControl
								placeholder="wp-content/"
								value={item}
								onChange={(value) =>
									this.updateRow(value, index)
								}
							/>
						</td>
						<td className="dd4t3-settings-repeat-table-delete">
							<Button
								variant="tertiary"
								className="dd4t3-settings-repeat-table-delete-button"
								isSmall={true}
								isDestructive={true}
								showTooltip={true}
								icon="trash"
								onClick={() => this.deleteRow(index)}
							></Button>
						</td>
					</tr>
				))}
				</tbody>
				<tfoot>
				<tr>
					<td colSpan="2">
						<Button
							variant="secondary"
							isSmall={true}
							icon="plus"
							onClick={() => this.addRow()}
							disabled={this.isLastItemEmpty()}
						>
							{__('Add', '404-to-301')}
						</Button>
					</td>
				</tr>
				</tfoot>
			</table>
		)
	}
}
