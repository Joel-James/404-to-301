/* global wp, dd4t3 */
import React from 'react'
import ListTable from '@/components/table/list-table'
import { ReactNotifications } from 'react-notifications-component'

const { __ } = wp.i18n

export default class Logs extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			logs: [],
			loaded: false,
		}
	}

	render() {
		return (
			<>
				<ReactNotifications />
                <ListTable/>
			</>
		)
	}
}
