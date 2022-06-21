/* global wp */
import React from 'react'
import {
	Button,
	Modal,
	Flex,
	ButtonGroup,
	BaseControl,
} from '@wordpress/components'

const { __ } = wp.i18n

const StatusButtonGroup = ({ current, onChange }) => {
	let statuses = {
		global: __('Global', '404-to-301'),
		enabled: __('Enable', '404-to-301'),
		disabled: __('Disable', '404-to-301'),
	}
	return (
		<>
			{Object.keys(statuses).map((key) => (
				<Button
					key={key}
					variant={key === current ? 'primary' : 'secondary'}
					onClick={() => onChange(key)}
				>
					{statuses[key]}
				</Button>
			))}
		</>
	)
}

export default class EditModal extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			logStatus: this.props.log.log_status,
			emailStatus: this.props.log.email_status,
			redirectStatus: this.props.log.redirect_status,
		}
	}

	closeModal() {}

	render() {
		return (
			<Modal
				title={__('Edit log', '404-to-301')}
				onRequestClose={this.props.onClose}
			>
				<BaseControl
					label={__('Log status', '404-to-301')}
					help="Do you want to enable logging"
				>
					<ButtonGroup>
						<StatusButtonGroup
							current={this.state.logStatus}
							onChange={(status) =>
								this.setState({ logStatus: status })
							}
						/>
					</ButtonGroup>
				</BaseControl>

				<BaseControl
					label={__('Email status', '404-to-301')}
					help="Do you want to enable email"
				>
					<ButtonGroup>
						<StatusButtonGroup
							current={this.state.emailStatus}
							onChange={(status) =>
								this.setState({ emailStatus: status })
							}
						/>
					</ButtonGroup>
				</BaseControl>

				<BaseControl
					label={__('Redirect status', '404-to-301')}
					help="Do you want to enable redirect"
				>
					<ButtonGroup>
						<StatusButtonGroup
							current={this.state.redirectStatus}
							onChange={(status) =>
								this.setState({ redirectStatus: status })
							}
						/>
					</ButtonGroup>
				</BaseControl>

				<Flex direction="row" justify="flex-end">
					<Button variant="secondary" onClick={this.props.onClose}>
						{__('Cancel', '404-to-301')}
					</Button>
					<Button variant="primary" onClick={this.props.onSave}>
						{__('Save Changes', '404-to-301')}
					</Button>
				</Flex>
			</Modal>
		)
	}
}
