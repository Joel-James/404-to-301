import React from 'react'

const {__} = wp.i18n
const {
	Panel,
	PanelBody,
	PanelRow,
	BaseControl,
	ToggleControl
} = wp.components

export default class General extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			disableGuessing: false,
			monitorChanges: false,
			ipLogging: false,
		}
	}

	render() {
		return (
			<PanelBody
				title={__('General')}
			>
				<PanelRow>
					<ToggleControl
						checked={this.state.disableGuessing}
						label={__('Stop WordPress from guessing URLs', '404-to-301')}
						help={__('WordPress will automatically correct a 404 URL if it is misspelled and very close to an existing link, before marking it as a 404 error.', '404-to-301')}
						onChange={(checked) => this.setState({disableGuessing: checked})}
					/>
				</PanelRow>

				<PanelRow>
					<ToggleControl
						checked={this.state.monitorChanges}
						label={__('Monitor permalink changes and create redirects', '404-to-301')}
						help={__('New 404 errors can be created when you change an existing page/post permalink to a new one. Instead of waiting for someone to visit and create a 404 error, ww can create a redirect ourself to the new permalink.', '404-to-301')}
						onChange={(checked) => this.setState({monitorChanges: checked})}
					/>
				</PanelRow>

				<PanelRow>
					<ToggleControl
						checked={this.state.ipLogging}
						label={__('Do not log visitor\'s IP address', '404-to-301')}
						help={__('To respect visitor\'s privacy and comply with GDPR policies, you may disable a few functionalities of the plugin.', '404-to-301')}
						onChange={(checked) => this.setState({ipLogging: checked})}
					/>
				</PanelRow>

				<PanelRow>
					<BaseControl
						label={__('Exclusions', '404-to-301')}
						help={__('Use this option to exclude a URL from being detected as 404 by the plugin. It will be wildcard checked using', '404-to-301')}
					>
					</BaseControl>
				</PanelRow>
			</PanelBody>
		);
	}
}