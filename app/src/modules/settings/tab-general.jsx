/* global wp */
import React from 'react'
import { TabPanel } from '@wordpress/components'

const { __ } = wp.i18n

export default class TabGeneral extends React.Component {
	constructor(props) {
		super(props)
		this.state = {}
	}

	render() {
		return (
			<>
				<TabPanel
					className="my-tab-panel"
					activeClass="active-tab"
					tabs={ [
						{
							name: 'tab1',
							title: 'Tab 1',
							className: 'tab-one',
						},
						{
							name: 'tab2',
							title: 'Tab 2',
							className: 'tab-two',
						},
					] }
				>
					{ ( tab ) => <p>{ tab.title }</p> }
				</TabPanel>
			</>
		)
	}
}
