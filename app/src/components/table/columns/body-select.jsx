/* global wp */
import React from 'react'
import { useInstanceId } from '@wordpress/compose'

const { __ } = wp.i18n

const BodySelectColumn = ({ value, disabled }) => {
	const id = useInstanceId(BodySelectColumn)

	return (
		<th scope="row" className="check-column">
			<label className="screen-reader-text" htmlFor={`cb-select-${id}`}>
				{__('Select item', '404-to-301')}
			</label>
			<input
				id={`cb-select-${id}`}
				type="checkbox"
				name="item[]"
				value={value}
				disabled={disabled}
			/>
		</th>
	)
}

export default BodySelectColumn
