/* global wp */
import React, { useId } from 'react'
const { __ } = wp.i18n

const HeaderSelectColumn = () => {
	const id = useId()

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
