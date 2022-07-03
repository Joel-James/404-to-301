import React from 'react'
import { BodyColumn } from '@/components/table/table'

const AgentColumn = ({ log }) => {
	return <BodyColumn id="agent">{log.agent}</BodyColumn>
}

export default AgentColumn
