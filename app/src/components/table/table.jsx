import React from 'react'
import HeaderRow from './rows/header'
import LoaderRow from './rows/loader'
import BodyColumn from './columns/body'
import NavAction from './actions/nav-action'
import SearchBox from './actions/search-box'
import BodySelectColumn from './columns/body-select'

const ListTable = ({ children }) => {
	return (
		<table className="wp-list-table widefat fixed striped posts">
			{children}
		</table>
	)
}

export {
	ListTable,
	HeaderRow,
	LoaderRow,
	NavAction,
	SearchBox,
	BodyColumn,
	BodySelectColumn,
}
