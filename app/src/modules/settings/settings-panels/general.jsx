/* global wp */
import React from 'react'
import {
	PanelBody,
	PanelRow,
	BaseControl,
	ToggleControl,
} from '@wordpress/components'
import RepeatTable from './../components/repeat-table'

const { __ } = wp.i18n

export default class General extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			disableGuessing: false,
			monitorChanges: false,
			disableIp: false,
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
			<PanelBody title={__('General', '404-to-301')}>
				<PanelRow>
					<ToggleControl
						checked={settings.disable_guessing}
						label={__(
							'Stop WordPress from guessing URLs',
							'404-to-301'
						)}
						help={__(
							'WordPress will automatically correct a 404 URL if it is misspelled and very close to an existing link, before marking it as a 404 error.',
							'404-to-301'
						)}
						onChange={(checked) =>
							this.updateValue('disable_guessing', checked)
						}
					/>
				</PanelRow>

				<PanelRow>
					<ToggleControl
						checked={settings.monitor_changes}
						label={__(
							'Monitor permalink changes and create redirects',
							'404-to-301'
						)}
						help={__(
							'New 404 errors can be created when you change an existing page/post permalink to a new one. Instead of waiting for someone to visit and create a 404 error, ww can create a redirect ourself to the new permalink.',
							'404-to-301'
						)}
						onChange={(checked) =>
							this.updateValue('monitor_changes', checked)
						}
					/>
				</PanelRow>

				<PanelRow>
					<ToggleControl
						checked={settings.disable_ip}
						label={__(
							"Do not log visitor's IP address",
							'404-to-301'
						)}
						help={__(
							"To respect visitor's privacy and comply with GDPR policies, you may disable a few functionalities of the plugin.",
							'404-to-301'
						)}
						onChange={(checked) =>
							this.updateValue('disable_ip', checked)
						}
					/>
				</PanelRow>

				<PanelRow>
					<BaseControl
						label={__('Exclusions', '404-to-301')}
						help={__(
							'Use this option to exclude a URL from being detected as 404 by the plugin. It will be wildcard checked using',
							'404-to-301'
						)}
					>
						<RepeatTable
							items={settings.exclude_paths}
							onChange={(paths) =>
								this.updateValue('exclude_paths', paths)
							}
						/>
					</BaseControl>
				</PanelRow>
			</PanelBody>
		)
	}
}
