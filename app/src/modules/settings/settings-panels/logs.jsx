/* global wp */
import React from 'react'
import {
	PanelRow,
	PanelBody,
	ExternalLink,
	ToggleControl,
} from '@wordpress/components'

const { __ } = wp.i18n

export default class Logs extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			enable: false,
			skipDuplicates: false,
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
			<PanelBody title={__('Logs')}>
				<PanelRow>
					<ToggleControl
						checked={settings.logs_enabled}
						label={__('Enable logs for 404 errors', '404-to-301')}
						help={__(
							'This will be helpful for you to keep track of broken links to your website. You can also setup individual redirects for each 404s from the logs page.',
							'404-to-301'
						)}
						onChange={(checked) =>
							this.updateValue('logs_enabled', checked)
						}
					/>
				</PanelRow>

				<PanelRow>
					<ExternalLink href="#">{__('Get API Key')}</ExternalLink>
				</PanelRow>

				<PanelRow>
					<ToggleControl
						label={__(
							'Skip duplicate entries from the logs',
							'404-to-301'
						)}
						help={__(
							'You may get 100s of visits to an old or non-existing link on your website. This can create 100s of copies of the same 404 link. If you enable this, the duplicates will be skipped without affecting the redirects. This will be helpful to keep your database light.',
							'404-to-301'
						)}
						checked={settings.logs_skip_duplicates}
						onChange={(checked) =>
							this.updateValue('logs_skip_duplicates', checked)
						}
					/>
				</PanelRow>
			</PanelBody>
		)
	}
}
