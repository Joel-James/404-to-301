/* global wp */
import React from 'react'
import {
	Flex,
	Modal,
	Button,
	Spinner,
	Notice,
	TextControl,
	ToggleControl,
	SelectControl,
	__experimentalVStack as VStack,
	__experimentalHStack as HStack,
} from '@wordpress/components'
import notify from '@/helpers/notify'
import request from '@/helpers/request'

const { __ } = wp.i18n

export default class RedirectModal extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			saving: false,
			loading: false,
			source: this.props.log.url,
			destination: '',
			status: 'enabled',
			type: 301,
			redirect: {},
		}

		this.saveActions = this.saveActions.bind(this)
	}

	async componentDidMount() {
		if (this.props.log.redirect_id) {
			await this.getRedirect()
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

		let apiUrl = '/redirects/'
		if (this.props.log.redirect_id) {
			apiUrl + this.props.log.redirect_id
		}

		let log = this.props.log

		// Get the list of addons.
		await request
			.post(apiUrl, {
				source: this.state.source,
				destination: this.state.destination,
				type: this.state.type,
				status: this.state.status,
			})
			.then(function (response) {
				// Show notification.
				notify(__('Changes have been updated.', '404-to-301'))
				if (response.data.data.redirect_id) {
					log.redirect_id = response.data.data.redirect_id
				}
				// Close modal.
				self.props.onSave(log)
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
	}

	/**
	 * Save log actions using API.
	 *
	 * @since 4.0.0
	 */
	async getRedirect() {
		const self = this

		// Make progress button.
		this.setState({ loading: true })

		// Get the list of addons.
		await request
			.get('/redirects/' + this.props.log.redirect_id)
			.then(function (response) {
				self.setState({
					destination: response.data.data.destination,
					status: response.data.data.status,
					type: response.data.data.code,
				})
			})

		// Remove progress button.
		this.setState({ loading: false })
	}

	render() {
		return (
			<Modal
				title={__('Custom Redirect', '404-to-301')}
				style={{ maxWidth: '550px' }}
				className="dd4t3-logs-modal"
				onRequestClose={this.props.onClose}
			>
				{this.state.loading ? (
					<HStack alignment="center">
						<Spinner />
					</HStack>
				) : (
					<VStack spacing={3}>
						<Notice status="error">
							An unknown error occurred.
						</Notice>
						<TextControl
							label={__('Redirect from:', '404-to-301')}
							value={this.state.source}
							readOnly={true}
						/>

						<TextControl
							label={__('Redirect to:', '404-to-301')}
							help={__(
								'Enter a custom URL you would like this to get redirected.',
								'404-to-301'
							)}
							type="url"
							value={this.state.destination}
							onChange={(value) => this.setState({ destination: value })}
						/>

						<SelectControl
							label={__('Redirect type', '404-to-301')}
							help={__(
								'The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.',
								'404-to-301'
							)}
							value={this.state.type}
							options={[
								{ label: '301', value: 301 },
								{ label: '302', value: 302 },
								{ label: '404', value: 404 },
							]}
							onChange={(value) => this.setState({ type: value })}
							labelPosition="top"
						/>

						<ToggleControl
							checked={this.state.status === 'enabled'}
							label={__(
								'Enable or disable custom redirect',
								'404-to-301'
							)}
							help={__(
								'Do you want to redirect the 404 errors to a new page or URL?',
								'404-to-301'
							)}
							onChange={(checked) =>
								this.setState({
									status: checked ? 'enabled' : 'disabled',
								})
							}
						/>

						<Flex direction="row" justify="flex-end">
							<Button
								variant="secondary"
								onClick={this.props.onClose}
							>
								{__('Cancel', '404-to-301')}
							</Button>
							<Button
								variant="primary"
								icon={this.state.saving ? null : 'yes'}
								disabled={this.state.saving}
								isBusy={this.state.saving}
								onClick={this.saveActions}
							>
								{__('Save Changes', '404-to-301')}
							</Button>
						</Flex>
					</VStack>
				)}
			</Modal>
		)
	}
}
