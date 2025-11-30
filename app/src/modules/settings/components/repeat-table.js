import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { Button, TextControl } from '@wordpress/components'

const RepeatTable = ( { items, onChange } ) => {
	// Initialize state with props, but ensure it's an array.
	const [ rows, setRows ] = useState( items || [] )

	/**
	 * Handle the rows update.
	 *
	 * Also communicate with parent.
	 *
	 * @param {Array} newRows
	 */
	const handleRowsChange = ( newRows ) => {
		setRows( newRows )
		onChange( newRows )
	}

	/**
	 * Add new row to the table.
	 */
	const addRow = () => {
		const newRows = [ ...rows, '' ]

		handleRowsChange( newRows )
	}

	/**
	 * Update an item in the table.
	 *
	 * @param {string} value
	 * @param {number} index
	 */
	const updateRow = ( value, index ) => {
		// Create a copy of the rows.
		const newRows = [ ...rows ]

		// Modify the item in the copy.
		newRows[ index ] = value

		// Update the rows.
		handleRowsChange( newRows )
	}

	/**
	 * Delete one row using it's index.
	 *
	 * @param {number} index Index of item.
	 */
	const deleteRow = ( index ) => {
		// Exclude the specified item.
		const newRows = rows.filter( ( _, i ) => i !== index )

		// Update the rows.
		handleRowsChange( newRows )
	}

	/**
	 * Check if last item of the array is empty.
	 *
	 * @return {boolean} boolean
	 */
	const isLastRowEmpty = () => {
		return '' === rows.slice( -1 )[ 0 ]
	}

	return (
		<table className="duckdev-404-settings-repeat-table">
			<tbody>
				{ rows.map( ( item, index ) => (
					<tr key={ index }>
						<td>
							<TextControl
								__next40pxDefaultSize={ false }
								__nextHasNoMarginBottom
								value={ item }
								onChange={ ( value ) =>
									updateRow( value, index )
								}
							/>
						</td>
						<td className="duckdev-404-settings-repeat-table-delete">
							<Button
								variant="tertiary"
								className="duckdev-404-settings-repeat-table-delete-button"
								isSmall={ true }
								isDestructive={ true }
								showTooltip={ true }
								icon="trash"
								onClick={ () => deleteRow( index ) }
							></Button>
						</td>
					</tr>
				) ) }
			</tbody>
			<tfoot>
				<tr>
					<td colSpan="2">
						<Button
							variant="secondary"
							size="small"
							onClick={ () => addRow() }
							disabled={ isLastRowEmpty() }
						>
							{ __( 'Add More', '404-to-301' ) }
						</Button>
					</td>
				</tr>
			</tfoot>
		</table>
	)
}

export default RepeatTable
