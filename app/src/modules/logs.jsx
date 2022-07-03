/* global wp */
import React from 'react'
import LogRow from './logs/log-row'
import request from '@/helpers/request'
import { ReactNotifications } from 'react-notifications-component'
import {
	ListTable,
	HeaderRow,
	LoaderRow,
	NavAction,
	SearchBox,
} from '@/components/table/table'

const { __ } = wp.i18n

const columns = [
	{
		id: 'date',
		label: __('Date', '404-to-301'),
		isPrimary: true,
		isSortable: true,
	},
	{
		id: 'url',
		label: __('404 URL', '404-to-301'),
		isSortable: true,
	},
	{
		id: 'referrer',
		label: __('Referrer', '404-to-301'),
		isSortable: true,
	},
	{
		id: 'ip',
		label: __('IP Address', '404-to-301'),
	},
	{
		id: 'agent',
		label: __('User Agent', '404-to-301'),
	},
	{
		id: 'redirect',
		label: __('Redirect', '404-to-301'),
	},
	{
		id: 'actions',
		label: __('Actions', '404-to-301'),
	},
]

export default class Logs extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			logs: [],
			order: 'asc',
			orderBy: 'date',
			loading: false,
		}

		this.onFilter = this.onFilter.bind(this)
		this.onSearch = this.onSearch.bind(this)
		this.onSorting = this.onSorting.bind(this)
		this.onGroupBy = this.onGroupBy.bind(this)
		this.onBulkAction = this.onBulkAction.bind(this)
	}

	componentDidMount() {
		// Get the logs on first render.
		this.getLogs()
	}

	/**
	 * Get the latest list of error logs.
	 *
	 * This will enable the loader while processing
	 * the API request.
	 *
	 * @since 4.0.0
	 */
	async getLogs() {
		const self = this

		// Set loader.
		this.setState({ loading: true })

		// Get the logs from API.
		await request.get('/logs').then(function (response) {
			self.setState({ logs: response.data.data })
		})

		// Disable the loader.
		this.setState({ loading: false })
	}

	/**
	 * Update a log item on the state.
	 *
	 * @since 4.0.0
	 *
	 * @param {object} log Updated log item
	 */
	updateLog(log) {
		let logs = this.state.logs

		// Find index of specific log using findIndex method.
		let index = logs.findIndex((data) => data.log_id == log.log_id)

		// Replace the item with updated data.
		logs[index] = log

		// Update the states.
		this.setState({ logs: logs })
	}

	onSearch() {}

	onBulkAction() {}

	onGroupBy() {}

	onFilter() {}

	onSorting(order, orderBy) {
		this.setState({
			order: order,
			orderBy: orderBy,
		})
	}

	render() {
		return (
			<>
				<ReactNotifications />
				<form className="posts-filter">
					<SearchBox onSearch={this.onSearch} />

					<div className="tablenav top">
						<NavAction
							name="bulk_actions"
							label={__('Bulk Actions', '404-to-301')}
							options={{
								'': __('Bulk Actions', '404-to-301'),
								delete: __('Delete', '404-to-301'),
							}}
							onSubmit={this.onBulkAction}
						/>
						<NavAction
							name="group_by"
							label={__('Group By', '404-to-301')}
							options={{
								'': __('No grouping', '404-to-301'),
								url: __('Group by URL', '404-to-301'),
								ip: __('Group by IP', '404-to-301'),
								referrer: __('Group by Referrer', '404-to-301'),
							}}
							onSubmit={this.onGroupBy}
						/>
						<NavAction
							name="filter_by"
							label={__('Filter by', '404-to-301')}
							options={{
								'': __('No filtering', '404-to-301'),
							}}
							onSubmit={this.onFilter}
						/>
					</div>

					<ListTable>
						<thead>
							<HeaderRow
								columns={columns}
								order={this.state.order}
								orderBy={this.state.orderBy}
								orderChange={this.onSorting}
							/>
						</thead>

						<tbody id="the-list">
							{this.state.loading ? (
								<LoaderRow colspan={columns.length} />
							) : (
								this.state.logs.map((log) => (
									<LogRow
										key={log.id}
										log={log}
										onDelete={() => this.getLogs()}
										onUpdate={(log) => this.updateLog(log)}
									/>
								))
							)}
						</tbody>
						<tfoot>
							<HeaderRow
								columns={columns}
								order={this.state.order}
								orderBy={this.state.orderBy}
								orderChange={this.onSorting}
							/>
						</tfoot>
					</ListTable>
					<div className="tablenav bottom">
						<NavAction
							name="bulk_actions"
							label={__('Bulk Actions', '404-to-301')}
							options={{
								'': __('Bulk Actions', '404-to-301'),
								delete: __('Delete', '404-to-301'),
							}}
							onSubmit={this.onBulkAction}
						/>
					</div>
				</form>
			</>
		)
	}
}
