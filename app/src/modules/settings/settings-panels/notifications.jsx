/* global wp */
import React from 'react'
import {
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl,
} from '@wordpress/components'

const { __ } = wp.i18n

export default class Notifications extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			enable: false,
			recipient: '',
		}
	}

	/**
	 * Update a field value in state.
	 *
	 * @param {string} field Field name.
	 * @param {mixed} value Field value.
	 *
	 * @since 4.0.0
	 */
	updateValue(field, value) {
		// Update field value.
		this.props.onUpdate(field, value)
	}

	render() {
		const settings = this.props.settings

		return (
			<PanelBody title={__('Notifications', '404-to-301')}>
				<PanelRow>
					<ToggleControl
						checked={settings.email_enabled}
						label={__(
							'Enable email notifications for 404 errors',
							'404-to-301'
						)}
						help={__(
							'Do you want to receive and email notification for each 404 errors?',
							'404-to-301'
						)}
						onChange={(checked) =>
							this.updateValue('email_enabled', checked)
						}
					/>
				</PanelRow>

				<PanelRow>
					<TextControl
						label={__('Recipient email', '404-to-301')}
						help={__(
							'Enter the email address where you want to get the email notification.',
							'404-to-301'
						)}
						type="email"
						value={settings.email_recipient}
						onChange={(value) =>
							this.updateValue('email_recipient', value)
						}
					/>
				</PanelRow>
			</PanelBody>
		)
	}
}
