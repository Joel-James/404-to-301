/* global wp, redirectpress */
import React from 'react'
import classNames from 'classnames'
import Tooltip from '@/components/tooltip'
import { BodyColumn } from '@/components/table/table'

const { __ } = wp.i18n

const RedirectColumn = ({ log }) => {
	let global = redirectpress.settings.redirect_enabled
	let hasRedirect = !!log.redirect_id
	let isEnabled = log.redirect_status === 'enabled'
	// Consider global status.
	if ('global' === log.redirect_status) {
		isEnabled = global
	}

	let classes = classNames({
		'redirectpress-logs-tag': true,
		'tag-red': 'disabled' === log.redirect_status,
		'tag-green': isEnabled,
	})

	return (
		<BodyColumn id="redirect">
			{log.redirect_status === 'global' ? (
				<Tooltip
					text={
						isEnabled
							? __('Global redirect active', '404-to-301')
							: __('Global redirect inactive', '404-to-301')
					}
				>
					<span className={classes}>
						{__('Global', '404-to-301')}
					</span>
				</Tooltip>
			) : hasRedirect ? (
				<Tooltip
					text={
						isEnabled
							? __('Custom redirect active', '404-to-301')
							: __(
								'Custom redirect setup but disabled',
								'404-to-301'
							)
					}
				>
					<span className={classes}>
						{__('Custom', '404-to-301')}
					</span>
				</Tooltip>
			) : (
				<Tooltip
					text={
						isEnabled
							? __('Redirect is enabled', '404-to-301')
							: __('Redirect is disabled', '404-to-301')
					}
				>
					<span className={classes}>
						{isEnabled
							? __('Enabled', '404-to-301')
							: __('Disabled', '404-to-301')}
					</span>
				</Tooltip>
			)}
		</BodyColumn>
	)
}

export default RedirectColumn
