/* global wp */
import React from 'react'
import {
	Flex,
	Modal,
	Button,
	SelectControl,
	__experimentalSpacer as Spacer,
	__experimentalText as Text,
	__experimentalVStack as VStack,
} from '@wordpress/components'
import notify from '@/helpers/notify'
import request from '@/helpers/request'

const { __ } = wp.i18n

// Status selection component.
const StatusSelection = ({ label, help, current, onChange }) => {
	let options = [
		{
			label: __('Global', '404-to-301'),
			options: {
				global: __('Use global setting', '404-to-301'),
			},
		},
		{
			label: __('Custom', '404-to-301'),
			options: {
				enabled: __('Enable', '404-to-301'),
				disabled: __('Disable', '404-to-301'),
			},
		},
	]

	return (
		<SelectControl
			label={label}
			help={help}
			value={current}
			onChange={(status) => onChange(status)}
			labelPosition="top"
		>
			{options.map((group) => (
				<optgroup key={group.label} label={group.label}>
					{Object.keys(group.options).map((key) => (
						<option key={key} value={key}>
							{group.options[key]}
						</option>
					))}
				</optgroup>
			))}
		</SelectControl>
	)
}

export default class ConfigureModal extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			saving: false,
			logStatus: this.props.log.log_status,
			emailStatus: this.props.log.email_status,
			redirectStatus: this.props.log.redirect_status,
		}
	}

	/**
	 * Save log actions using API.
	 *
	 * @since 4.0.0
	 */
	async saveActions() {
		const self = this

		// Make progress button.
		this.setState({ saving: true })

		// Get the list of addons.
		await request
			.post('/logs/' + self.props.log.log_id, {
				log_status: self.state.logStatus,
				email_status: self.state.emailStatus,
				redirect_status: self.state.redirectStatus,
			})
			.then(function () {
				// Show notification.
				notify(__('Changes have been updated.', '404-to-301'))
			})
			.catch(function () {
				// Show notification.
				notify(
					__('Update failed. Please try again.', '404-to-301'),
					'error'
				)
			})

		// Remove progress button.
		this.setState({ saving: false })

		// Trigger save event.
		this.props.onSave()
	}

	render() {
		return (
			<Modal
				title={__('Configure actions', '404-to-301')}
				onRequestClose={this.props.onClose}
				style={{ maxWidth: '550px' }}
				className="dd4t3-logs-modal"
			>
				<VStack spacing={3}>
					<Spacer>
						<Text>
							<strong>
								{__('Customize for:', '404-to-301')}
							</strong>
						</Text>
					</Spacer>
					<Text>
						<a href="javascript:void(0)">{this.props.log.url}</a>
					</Text>

					<hr />

					<StatusSelection
						label={__('Error logging:', '404-to-301')}
						help={__('Do you want to enable logging', '404-to-301')}
						current={this.state.logStatus}
						onChange={(status) =>
							this.setState({ logStatus: status })
						}
					/>

					<StatusSelection
						label={__('Email notification:', '404-to-301')}
						help={__('Do you want to enable email', '404-to-301')}
						current={this.state.emailStatus}
						onChange={(status) =>
							this.setState({ emailStatus: status })
						}
					/>

					<StatusSelection
						label={__('Redirect:', '404-to-301')}
						help={__(
							'Do you want to enable redirect',
							'404-to-301'
						)}
						current={this.state.redirectStatus}
						onChange={(status) =>
							this.setState({ redirectStatus: status })
						}
					/>

					<Flex direction="row" justify="flex-end">
						<Button
							variant="tertiary"
							onClick={this.props.onClose}
						>
							{__('Cancel', '404-to-301')}
						</Button>
						<Button
							variant="primary"
							icon={this.state.saving ? null : 'yes'}
							disabled={this.state.saving}
							isBusy={this.state.saving}
							onClick={() => this.saveActions()}
						>
							{this.state.saving
								? __('Saving..', '404-to-301')
								: __('Save Changes', '404-to-301')}
						</Button>
					</Flex>
				</VStack>
			</Modal>
		)
	}
}
