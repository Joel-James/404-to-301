import React from 'react'
import LogsPanel from './panels/logs'
import GeneralPanel from './panels/general'
import RedirectsPanel from './panels/redirects'
import NotificationsPanel from './panels/notifications'

const {
	Notice,
	Dashicon
} = wp.components

const {__} = wp.i18n
const {Button} = wp.components
const axios = require('axios');
const axiosConfig = {
	baseURL: dd4t3.rest.base,
	headers: {
		'X-WP-Nonce': dd4t3.rest.nonce,
	},
}
// Create new axios instance.
const request = axios.create(axiosConfig)

export default class Settings extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			saving: false,
			settings: this.props.settings
		}

		// Bind updates.
		this.updateSetting = this.updateSetting.bind(this);
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
				[field]: value
			}
		});
	}

	/**
	 * Save current settings using API.
	 *
	 * @since 4.0.0
	 */
	async saveSettings() {
		const self = this

		// Make progress button.
		this.setState({saving: true})

		window.scrollTo({ top: 0, behavior: 'smooth' });

		// Get the list of addons.
		await request.post('/settings', {
			value: self.state.settings
		})
		.then(function (response) {
			self.setState({settings: response.data.data})
		})

		// Remove progress button.
		this.setState({saving: false})
	}

	render() {
		return (
			<>
				<Notice status="success">
					<p>
						<Dashicon icon="yes" />
						Settings has been updated.
					</p>
				</Notice>
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

				<Button
					disabled={this.state.saving}
					isBusy={this.state.saving}
					variant="primary"
					icon={this.state.saving ? null : 'yes'}
					onClick={() => this.saveSettings()}
				>
					{this.state.saving ? __('Saving..', '404-to-301') : __('Save Changes', '404-to-301')}
				</Button>
			</>
		);
	}
}