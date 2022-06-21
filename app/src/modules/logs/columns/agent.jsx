import React from 'react'
import BodyColumn from '@/components/table/columns/body-column'

const AgentColumn = ({ log }) => {
	return <BodyColumn id="agent">{log.agent}</BodyColumn>
}

export default AgentColumn
