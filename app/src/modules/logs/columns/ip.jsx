import React from 'react'
import BodyColumn from '@/components/table/columns/body-column'

const IpColumn = ({ log }) => {
	return <BodyColumn id="ip">{log.ip ? log.ip : '-'}</BodyColumn>
}

export default IpColumn
