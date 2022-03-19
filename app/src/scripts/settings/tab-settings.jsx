/* global wp */
import React from 'react'
import LogsPanel from './settings-panels/logs'
import GeneralPanel from './settings-panels/general'
import RedirectsPanel from './settings-panels/redirects'
import NotificationsPanel from './settings-panels/notifications'
import notify from '@/helpers/notify'
import request from '@/helpers/request'
import { Button } from '@wordpress/components'

const { __ } = wp.i18n

export default class TabSettings extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			saving: false,
			settings: this.props.settings,
		}

		// Bind updates.
		this.updateSetting = this.updateSetting.bind(this)
	}

	/**
	 * Update a field value in state.
	 *
	 * @param {string} field Field name.
	 * @param {mixed} value Field value.
	 *
	 * @since 4.0.0
	 */
	updateSetting(field, value) {
		// Update field value.
		this.setState({
			settings: {
				...this.state.settings,
				[field]: value,
			},
		})
	}

	/**
	 * Save current settings using API.
	 *
	 * @since 4.0.0
	 */
	async saveSettings() {
		const self = this

		// Make progress button.
		this.setState({ saving: true })

		// Get the list of addons.
		await request
			.post('/settings', {
				value: self.state.settings,
			})
			.then(function (response) {
				self.setState({ settings: response.data.data })
				// Show notification.
				notify(__('Changes have been updated.', '404-to-301'))
			})
			.catch(function (error) {
				// Show notification.
				notify(
					__(
						'Could not save the changes. Please try again.',
						'404-to-301'
					),
					'error'
				)
			})

		// Remove progress button.
		this.setState({ saving: false })
	}

	render() {
		return (
			<>
				<RedirectsPanel
					settings={this.state.settings}
					onUpdate={this.updateSetting}
				/>

				<LogsPanel
					settings={this.state.settings}
					onUpdate={this.updateSetting}
				/>

				<NotificationsPanel
					settings={this.state.settings}
					onUpdate={this.updateSetting}
				/>

				<GeneralPanel
					settings={this.state.settings}
					onUpdate={this.updateSetting}
				/>

				<div className="dd4t3-footer">
					<Button
						disabled={this.state.saving}
						isBusy={this.state.saving}
						variant="primary"
						icon={this.state.saving ? null : 'yes'}
						onClick={() => this.saveSettings()}
					>
						{this.state.saving
							? __('Saving..', '404-to-301')
							: __('Save Changes', '404-to-301')}
					</Button>
				</div>
			</>
		)
	}
}
