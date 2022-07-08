import React from 'react'
import moment from 'moment'
import { BodyColumn } from '@/components/table/table'

const DateColumn = ({ log }) => {
	// Init moment.
	let date = moment(log.created_at)

	return (
		<BodyColumn id="date">
			<span title={log.created_at}>
				{date.format('MMM d, YYYY [at] h:mm A')}
			</span>
		</BodyColumn>
	)
}

export default DateColumn
