/* global wp */
import React, { useState } from 'react'
import { useInstanceId } from '@wordpress/compose'

const { __ } = wp.i18n

const NavAction = ({ name, label, options, onSubmit, submitLabel }) => {
	const id = useInstanceId(NavAction)
	const [action, setAction] = useState('')

	return (
		<div className="alignleft actions bulkactions">
			<label
				htmlFor={`action-${name}-${id}`}
				className="screen-reader-text"
			>
				{label}
			</label>
			<select
				name="action"
				id={`action-${name}-${id}`}
				value={action}
				onChange={(ev) => setAction(ev.target.value)}
			>
				{Object.keys(options).map((key) => (
					<option key={key} value={key}>
						{options[key]}
					</option>
				))}
			</select>
			<button
				type="button"
				className="button action"
				disabled={!action}
				onClick={() => onSubmit(action)}
			>
				{submitLabel}
			</button>
		</div>
	)
}

NavAction.defaultProps = {
	name: 'selector',
	label: __('Actions', '404-to-301'),
	submitLabel: __('Apply', '404-to-301'),
}

export default NavAction
