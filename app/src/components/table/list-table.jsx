/* global wp */
import React from 'react'
import HeaderRow from './rows/header'

const { __ } = wp.i18n

export default class ListTable extends React.Component {
	constructor(props) {
		super(props)
		this.state = {}
	}

	render() {
		return (
			<table className="wp-list-table widefat fixed striped posts">
				<thead>
					<HeaderRow/>
				</thead>

				<tbody id="the-list">
					<tr
						id="post-1"
						className="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-Dummy category"
					>
						<th scope="row" className="check-column">
							<label
								className="screen-reader-text"
								htmlFor="cb-select-1"
							>
								Select Post #1
							</label>
							<input
								id="cb-select-1"
								type="checkbox"
								name="post[]"
								value="1"
							/>
							<div className="locked-indicator">
								<span
									className="locked-indicator-icon"
									aria-hidden="true"
								></span>
								<span className="screen-reader-text">
									“Post #1” is locked
								</span>
							</div>
						</th>
						<td
							className="title column-title has-row-actions column-primary page-title"
							data-colname="Title"
						>
							<div className="locked-info">
								<span className="locked-avatar"></span>
								<span className="locked-text"></span>
							</div>
							<strong>
								<a
									className="row-title"
									href="javascript:void(0)"
									aria-label="“Post #1” (Edit)"
								>
									Post #1
								</a>
							</strong>

							<div className="row-actions">
								<span className="edit">
									<a
										href="javascript:void(0)"
										aria-label="Edit “Post #1”"
									>
										Edit
									</a>{' '}
									|{' '}
								</span>
								<span className="inline hide-if-no-js">
									<button
										type="button"
										className="button-link editinline"
										aria-label="Quick edit “Post #1” inline"
										aria-expanded="false"
									>
										Quick Edit
									</button>{' '}
									|{' '}
								</span>
								<span className="trash">
									<a
										href="javascript:void(0)"
										className="submitdelete"
										aria-label="Move “Post #1” to the Trash"
									>
										Trash
									</a>{' '}
									|{' '}
								</span>
								<span className="view">
									<a
										href="javascript:void(0)"
										rel="bookmark"
										aria-label="View “Post #1”"
									>
										View
									</a>
								</span>
							</div>
							<button type="button" className="toggle-row">
								<span className="screen-reader-text">
									Show more details
								</span>
							</button>
						</td>
						<td
							className="author column-author"
							data-colname="Author"
						>
							<a href="javascript:void(0)">dummy@emailaddress</a>
						</td>
						<td
							className="categories column-categories"
							data-colname="Categories"
						>
							<a href="javascript:void(0)">Dummy category</a>
						</td>
						<td className="tags column-tags" data-colname="Tags">
							<span aria-hidden="true">—</span>
							<span className="screen-reader-text">No tags</span>
						</td>
						<td
							className="comments column-comments"
							data-colname="Comments"
						>
							<div className="post-com-count-wrapper">
								<a
									href="javascript:void(0)"
									className="post-com-count post-com-count-approved"
								>
									<span
										className="comment-count-approved"
										aria-hidden="true"
									>
										1
									</span>
									<span className="screen-reader-text">
										1 comment
									</span>
								</a>
								<span className="post-com-count post-com-count-pending post-com-count-no-pending">
									<span
										className="comment-count comment-count-no-pending"
										aria-hidden="true"
									>
										0
									</span>
									<span className="screen-reader-text">
										No pending comments
									</span>
								</span>
							</div>
						</td>
						<td className="date column-date" data-colname="Date">
							Published
							<br />
							<abbr title="2019/08/22 9:00:46 am">
								2 hours ago
							</abbr>
						</td>
					</tr>{' '}
					<tr
						id="post-2"
						className="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-Dummy category"
					>
						<th scope="row" className="check-column">
							<label
								className="screen-reader-text"
								htmlFor="cb-select-1"
							>
								Select Post #2
							</label>
							<input
								id="cb-select-1"
								type="checkbox"
								name="post[]"
								value="1"
							/>
							<div className="locked-indicator">
								<span
									className="locked-indicator-icon"
									aria-hidden="true"
								></span>
								<span className="screen-reader-text">
									“Post #2” is locked
								</span>
							</div>
						</th>
						<td
							className="title column-title has-row-actions column-primary page-title"
							data-colname="Title"
						>
							<div className="locked-info">
								<span className="locked-avatar"></span>
								<span className="locked-text"></span>
							</div>
							<strong>
								<a
									className="row-title"
									href="javascript:void(0)"
									aria-label="“Post #2” (Edit)"
								>
									Post #2
								</a>
							</strong>

							<div className="row-actions">
								<span className="edit">
									<a
										href="javascript:void(0)"
										aria-label="Edit “Post #2”"
									>
										Edit
									</a>{' '}
									|{' '}
								</span>
								<span className="inline hide-if-no-js">
									<button
										type="button"
										className="button-link editinline"
										aria-label="Quick edit “Post #2” inline"
										aria-expanded="false"
									>
										Quick Edit
									</button>{' '}
									|{' '}
								</span>
								<span className="trash">
									<a
										href="javascript:void(0)"
										className="submitdelete"
										aria-label="Move “Post #2” to the Trash"
									>
										Trash
									</a>{' '}
									|{' '}
								</span>
								<span className="view">
									<a
										href="javascript:void(0)"
										rel="bookmark"
										aria-label="View “Post #2”"
									>
										View
									</a>
								</span>
							</div>
							<button type="button" className="toggle-row">
								<span className="screen-reader-text">
									Show more details
								</span>
							</button>
						</td>
						<td
							className="author column-author"
							data-colname="Author"
						>
							<a href="javascript:void(0)">dummy@emailaddress</a>
						</td>
						<td
							className="categories column-categories"
							data-colname="Categories"
						>
							<a href="javascript:void(0)">Dummy category</a>
						</td>
						<td className="tags column-tags" data-colname="Tags">
							<span aria-hidden="true">—</span>
							<span className="screen-reader-text">No tags</span>
						</td>
						<td
							className="comments column-comments"
							data-colname="Comments"
						>
							<div className="post-com-count-wrapper">
								<a
									href="javascript:void(0)"
									className="post-com-count post-com-count-approved"
								>
									<span
										className="comment-count-approved"
										aria-hidden="true"
									>
										1
									</span>
									<span className="screen-reader-text">
										1 comment
									</span>
								</a>
								<span className="post-com-count post-com-count-pending post-com-count-no-pending">
									<span
										className="comment-count comment-count-no-pending"
										aria-hidden="true"
									>
										0
									</span>
									<span className="screen-reader-text">
										No pending comments
									</span>
								</span>
							</div>
						</td>
						<td className="date column-date" data-colname="Date">
							Published
							<br />
							<abbr title="2019/08/22 9:00:46 am">
								2 hours ago
							</abbr>
						</td>
					</tr>{' '}
				</tbody>
				<tfoot>
					<tr>
						<td className="manage-column column-cb check-column">
							<label
								className="screen-reader-text"
								htmlFor="cb-select-all-2"
							>
								Select All
							</label>
							<input id="cb-select-all-2" type="checkbox" />
						</td>
						<th
							scope="col"
							className="manage-column column-title column-primary sortable desc"
						>
							<span>Title</span>
						</th>
						<th scope="col" className="manage-column column-author">
							Author
						</th>
						<th
							scope="col"
							className="manage-column column-categories"
						>
							Categories
						</th>
						<th scope="col" className="manage-column column-tags">
							Tags
						</th>
						<th
							scope="col"
							className="manage-column column-comments num sortable desc"
						>
							<span>
								<span
									className="vers comment-grey-bubble"
									title="Comments"
								>
									<span className="screen-reader-text">
										Comments
									</span>
								</span>
							</span>
						</th>
						<th
							scope="col"
							className="manage-column column-date sortable asc"
						>
							<a href="javascript:void(0)">
								<span>Date</span>
								<span className="sorting-indicator"></span>
							</a>
						</th>
					</tr>
				</tfoot>
			</table>
		)
	}
}
