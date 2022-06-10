import React from 'react'
const { __ } = wp.i18n

export default class BodyRow extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			columns: {
				cb: {
					hidden: true,
					classes: [],
					label: __('Select All', '404-to-301'),
				},
				title: {
					hidden: false,
					classes: [],
					label: __('Title', '404-to-301'),
				},
				author: {
					hidden: false,
					classes: [],
					label: __('Author', '404-to-301'),
				},
				categories: {
					hidden: false,
					classes: [],
					label: __('Categories', '404-to-301'),
				},
				tags: {
					hidden: false,
					classes: [],
					label: __('Tags', '404-to-301'),
				},
				comments: {
					hidden: false,
					classes: [],
					label: __('Comments', '404-to-301'),
				},
				date: {
					hidden: false,
					classes: [],
					label: __('Date', '404-to-301'),
				},
			},
		}
	}

	render() {
		return (
			<tr>
				<td id="cb" className="manage-column column-cb check-column">
					<label
						className="screen-reader-text"
						htmlFor="cb-select-all-1"
					>
						Select All
					</label>
					<input id="cb-select-all-1" type="checkbox" />
				</td>
				<th
					scope="col"
					id="title"
					className="manage-column column-title column-primary sortable desc"
				>
					<span>Title</span>
				</th>
				<th
					scope="col"
					id="author"
					className="manage-column column-author"
				>
					Author
				</th>
				<th
					scope="col"
					id="categories"
					className="manage-column column-categories"
				>
					Categories
				</th>
				<th scope="col" id="tags" className="manage-column column-tags">
					Tags
				</th>
				<th
					scope="col"
					id="comments"
					className="manage-column column-comments num sortable desc"
				>
					<span>
						<span
							className="vers comment-grey-bubble"
							title="Comments"
						>
							<span className="screen-reader-text">Comments</span>
						</span>
					</span>
				</th>
				<th
					scope="col"
					id="date"
					className="manage-column column-date sortable asc"
				>
					<a href="javascript:void(0)">
						<span>Date</span>
						<span className="sorting-indicator"></span>
					</a>
				</th>
			</tr>
		)
	}
}
