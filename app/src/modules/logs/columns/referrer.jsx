import React from 'react'
import BodyColumn from '@/components/table/columns/body-column'

const ReferrerColumn = ({ log }) => {
	return (
		<BodyColumn id="referrer">
			<a href={log.referrer} target="_blank" rel="noreferrer">
				{log.referrer}
			</a>
		</BodyColumn>
	)
}

export default ReferrerColumn
