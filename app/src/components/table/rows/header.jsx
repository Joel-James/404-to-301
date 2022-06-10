/* global wp */
import React from 'react'
const { __ } = wp.i18n

import HeaderColumn from './../columns/header-column'
import HeaderSelectColumn from './../columns/header-select'

export default class HeaderRow extends React.Component {
	constructor(props) {
		super(props)

		this.state = {}
	}

	render() {
		return (
			<tr>
				<HeaderSelectColumn />
				<HeaderColumn
					label={__('Title', '404-to-301')}
					id="title"
					classes={['column-primary', 'sortable', 'desc']}
				/>
				<HeaderColumn label={__('Author', '404-to-301')} id="author" />
				<HeaderColumn
					label={__('Categories', '404-to-301')}
					id="categories"
				/>
				<HeaderColumn label={__('Tags', '404-to-301')} id="tags" />
				<HeaderColumn
					label={__('Comments', '404-to-301')}
					id="comments"
					classes={['num', 'sortable', 'desc']}
				/>
				<HeaderColumn
					label={__('Date', '404-to-301')}
					id="date"
					classes={['sortable', 'desc']}
				/>
			</tr>
		)
	}
}
