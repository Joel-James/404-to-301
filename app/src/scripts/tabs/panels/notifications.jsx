import React from 'react'

const {__} = wp.i18n
const {
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl
} = wp.components

export default class Notifications extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			enable: false,
			recipient: '',
		}
	}

	render() {
		return (
			<PanelBody
				title={__('Notifications', '404-to-301')}
			>
				<PanelRow>
					<ToggleControl
						checked={this.state.enable}
						label={__('Enable email notifications for 404 errors', '404-to-301')}
						help={__('Do you want to receive and email notification for each 404 errors?', '404-to-301')}
						onChange={(checked) => this.setState({enable: checked})}
					/>
				</PanelRow>

				<PanelRow>
					<TextControl
						label={__('Recipient email', '404-to-301')}
						help={__('Enter the email address where you want to get the email notification.', '404-to-301')}
						onChange={(value) => this.setState({recipient: value})}
					/>
				</PanelRow>
			</PanelBody>
		);
	}
}