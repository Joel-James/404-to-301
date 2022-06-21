import React from 'react'
import BodyColumn from '@/components/table/columns/body-column'

const UrlColumn = ({ log }) => {
	return <BodyColumn id="url">{log.url}</BodyColumn>
}

export default UrlColumn
