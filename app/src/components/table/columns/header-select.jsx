/* global wp */
import React from 'react'
import { useInstanceId } from '@wordpress/compose'

const { __ } = wp.i18n

const HeaderSelectColumn = () => {
	const id = useInstanceId(HeaderSelectColumn)

	return (
		<td id="cb" className="manage-column column-cb check-column">
			<label
				className="screen-reader-text"
				htmlFor={`cb-select-all-${id}`}
			>
				{__('Select all', '404-to-301')}
			</label>
			<input id={`cb-select-all-${id}`} type="checkbox" />
		</td>
	)
}

export default HeaderSelectColumn
