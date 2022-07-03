import React from 'react'
import BodySelectColumn from './../columns/body-select'
import { Spinner, __experimentalHStack as HStack } from '@wordpress/components'

const LoaderRow = ({ colspan }) => {
	return (
		<tr>
			<BodySelectColumn value="" disabled={true} />
			<td className="column-loading" colSpan={colspan}>
				<HStack alignment="center">
					<Spinner />
				</HStack>
			</td>
		</tr>
	)
}

export default LoaderRow
