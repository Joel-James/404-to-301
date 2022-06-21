import React from 'react'
import BodyColumn from '@/components/table/columns/body-column'

const DateColumn = ({ log }) => {
	return <BodyColumn id="date">{log.created_at}</BodyColumn>
}

export default DateColumn
