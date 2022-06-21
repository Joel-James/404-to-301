/* global wp */
import React from 'react'
import request from '@/helpers/request'
import ListTable from '@/components/table/list-table'
import LogRow from './logs/log-row'
import { ReactNotifications } from 'react-notifications-component'

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
			loading: true,
		}
	}

	async componentDidMount() {
		const self = this

		// Get the logs.
		await request.get('/logs').then(function (response) {
			self.setState({ logs: response.data.data })
		})

		// Logs are loaded.
		this.setState({ loading: false })
	}

	render() {
		return (
			<>
				<ReactNotifications />
				<ListTable loading={this.state.loading} columns={columns}>
					{this.state.logs.map((log) => (
						<LogRow key={log.id} log={log} />
					))}
				</ListTable>
			</>
		)
	}
}
