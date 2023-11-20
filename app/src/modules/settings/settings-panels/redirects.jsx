/* global wp, redirectpress */
import React from 'react'
import {
	PanelBody,
	PanelRow,
	BaseControl,
	TextControl,
	RadioControl,
	ToggleControl,
	ComboboxControl,
} from '@wordpress/components'

const { __ } = wp.i18n

let pages = []
// Setup page list.
if (redirectpress.pages) {
	Object.keys(redirectpress.pages).forEach((id) => {
		pages.push({
			label: redirectpress.pages[id],
			value: id,
		})
	})
}

export default class RedirectsPanel extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			redirectPages: pages,
		}

		// Bind updates.
		this.updateValue = this.updateValue.bind(this)
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
		let types = []
		Object.keys(window.redirectpress.types).forEach((type) => {
			types.push({
				value: type,
				label: window.redirectpress.types[type],
			})
		})

		return (
			<PanelBody title={__('Redirects', '404-to-301')}>
				<PanelRow>
					<ToggleControl
						checked={settings.redirect_enabled}
						label={__(
							'Enable redirects for 404 errors',
							'404-to-301'
						)}
						help={__(
							'Do you want to redirect the 404 errors to a new page or URL?',
							'404-to-301'
						)}
						onChange={(checked) =>
							this.updateValue('redirect_enabled', checked)
						}
						value="ddd"
					/>
				</PanelRow>

				<PanelRow>
					<RadioControl
						label={__('Redirect type', '404-to-301')}
						help={__(
							'The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.',
							'404-to-301'
						)}
						selected={settings.redirect_type}
						options={types}
						onChange={(selected) =>
							this.updateValue('redirect_type', selected)
						}
					/>
				</PanelRow>

				<PanelRow>
					<RadioControl
						label={__('Redirect target')}
						help={__(
							'From the target types, choose where you want to redirect the 404 errors to.',
							'4045-to-301'
						)}
						selected={settings.redirect_target}
						options={[
							{ label: __('Page', '404-to-301'), value: 'page' },
							{ label: __('Link', '404-to-301'), value: 'link' },
						]}
						onChange={(selected) =>
							this.updateValue('redirect_target', selected)
						}
					/>
				</PanelRow>

				{settings.redirect_target === 'page' ? (
					<PanelRow>
						<ComboboxControl
							label={__('Select page', '404-to-301')}
							help={__(
								'Enter the email address where you want to get the email notification.',
								'404-to-301'
							)}
							value={settings.redirect_page}
							onChange={(selected) =>
								this.updateValue('redirect_page', selected)
							}
							options={this.state.redirectPages}
							onFilterValueChange={(inputValue) =>
								this.setState({
									redirectPages: pages.filter((option) =>
										option.label
											.toLowerCase()
											.startsWith(
												inputValue.toLowerCase()
											)
									),
								})
							}
						/>
					</PanelRow>
				) : (
					<PanelRow>
						<BaseControl
							label={__('Custom URL', '404-to-301')}
							help={__(
								'Enter the email address where you want to get the email notification.',
								'404-to-301'
							)}
							id="redirectpress-custom-url"
							className="redirectpress-full-width"
						>
							<TextControl
								value={settings.redirect_link}
								id="redirectpress-custom-url"
								type="url"
								placeholder={__('https://google.com')}
								onChange={(value) =>
									this.updateValue('redirect_link', value)
								}
							/>
						</BaseControl>
					</PanelRow>
				)}
			</PanelBody>
		)
	}
}
