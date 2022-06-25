/* global wp */
import React from 'react'
import Tooltip from '@/components/tooltip'
import { Dashicon } from '@wordpress/components'
import BodyColumn from '@/components/table/columns/body-column'

const { __ } = wp.i18n

const RedirectColumn = ({ log }) => {
	return (
		<BodyColumn id="redirect">
			{log.redirect_status === 'disabled' ? (
				<Tooltip text={__('Redirect is disabled', '404-to-301')}>
					<Dashicon icon="dismiss" className="dd4t3-logs-red" />
				</Tooltip>
			) : (
				<Tooltip
					text={
						log.redirect_id
							? __('Custom redirect active', '404-to-301')
							: __('Default redirect active', '404-to-301')
					}
				>
					<Dashicon
						icon="yes-alt"
						className={
							log.redirect_id
								? 'dd4t3-logs-green'
								: 'dd4t3-logs-grey'
						}
					/>
				</Tooltip>
			)}
		</BodyColumn>
	)
}

export default RedirectColumn
