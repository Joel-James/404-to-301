import React from 'react'

import IpColumn from './columns/ip'
import UrlColumn from './columns/url'
import DateColumn from './columns/date'
import AgentColumn from './columns/agent'
import ActionsColumn from './columns/actions'
import RedirectColumn from './columns/redirect'
import ReferrerColumn from './columns/referrer'
import BodySelectColumn from '@/components/table/columns/body-select'

export default class LogRow extends React.Component {
	constructor(props) {
		super(props)

		this.state = {}
	}

	render() {
		let log = this.props.log

		return (
			<tr>
				<BodySelectColumn value={log.id} />
				<DateColumn log={log} />
				<UrlColumn log={log} />
				<ReferrerColumn log={log} />
				<IpColumn log={log} />
				<AgentColumn log={log} />
				<RedirectColumn log={log} />
				<ActionsColumn log={log} />
			</tr>
		)
	}
}
