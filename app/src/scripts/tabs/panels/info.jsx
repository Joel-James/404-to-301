import React from 'react'
const { __ } = wp.i18n
const {
	Button,
	PanelBody,
} = wp.components

export default class Info extends React.Component {
	constructor(props) {
		super(props);
		this.state = {}
	}

	render() {
		return (
			<PanelBody>
				<div className="dd4t3-info">
					<h2>{ __( 'Got a question for us?' ) }</h2>

					<p>{ __( 'We would love to help you out if you need any help.' ) }</p>

					<div className="dd4t3-info-button-group">
						<Button
							isDefault
							target="_blank"
							href="#"
						>
							{ __( 'Ask a question' ) }
						</Button>

						<Button
							isDefault
							target="_blank"
							href="#"
						>
							{ __( 'Leave a review' ) }
						</Button>
					</div>
				</div>
			</PanelBody>
		);
	}
}