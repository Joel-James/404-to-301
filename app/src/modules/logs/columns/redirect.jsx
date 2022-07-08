/* global wp, dd4t3 */
import React from 'react'
import Tooltip from '@/components/tooltip'
import { Dashicon } from '@wordpress/components'
import { BodyColumn } from '@/components/table/table'

const { __ } = wp.i18n

const RedirectColumn = ({ log }) => {
	let status = log.redirect_status
	let global = dd4t3.settings.redirect_enabled
	let hasRedirect = !!log.redirect_id

	return (
		<BodyColumn id="redirect">
			{status === 'global' ? (
				<Tooltip
					text={
						global
							? __('Global redirect active', '404-to-301')
							: __('Global redirect inactive', '404-to-301')
					}
				>
					<Dashicon
						icon={global ? 'yes-alt' : 'dismiss'}
						className="dd4t3-logs-grey"
					/>
				</Tooltip>
			) : status === 'enabled' ? (
				<Tooltip
					text={
						hasRedirect
							? __('Custom redirect active', '404-to-301')
							: __('Redirect is enabled', '404-to-301')
					}
				>
					<Dashicon
						icon="yes-alt"
						className={
							hasRedirect ? 'dd4t3-logs-green' : 'dd4t3-logs-grey'
						}
					/>
				</Tooltip>
			) : (
				<Tooltip text={__('Redirect is disabled', '404-to-301')}>
					<Dashicon icon="dismiss" className="dd4t3-logs-red" />
				</Tooltip>
			)}
		</BodyColumn>
	)
}

export default RedirectColumn
