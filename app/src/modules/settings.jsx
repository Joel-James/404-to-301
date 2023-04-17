/* global wp, dd4t3 */
import React from 'react'
import request from '@/helpers/request'
import TabInfo from './settings/tab-info'
import TabGeneral from './settings/tab-general'
import TabAddons from './settings/tab-addons'
import TabSettings from './settings/tab-settings'
import NavTabs from './settings/components/nav-tabs'
import { ReactNotifications } from 'react-notifications-component'

const { __ } = wp.i18n
import { Spinner, Placeholder } from '@wordpress/components'

export default class Settings extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			addons: [],
			settings: {},
			loaded: false,
			currentTab: 'settings',
		}
	}

	async componentDidMount() {
		const self = this

		await Promise.all([
			// Get the list of addons.
			request.get('/data/addons').then(function (response) {
				self.setState({ addons: response.data.data })
			}),
			// Get the list of addons.
			request.get('/settings').then(function (response) {
				self.setState({ settings: response.data.data })
			}),
		])

		// Data is loaded.
		self.setState({ loaded: true })
	}

	render() {
		return (
			<>
				<ReactNotifications />
				<div className="dd4t3-settings-header">
					<div className="dd4t3-settings-title-section">
						<h1>404 to 301 - Settings</h1>
						<abbr
							title={'Version: ' + dd4t3.version}
							className="version"
						>
							v{dd4t3.version}
						</abbr>
					</div>

					{this.state.loaded && (
						<NavTabs
							current={this.state.currentTab}
							navs={{
								settings: __('Redirects', '404-to-301'),
								addons: __('Logs', '404-to-301'),
								info: __('Email', '404-to-301'),
								general: __('General', '404-to-301'),
							}}
							onChange={(tab) => {
								this.setState({ currentTab: tab })
							}}
						/>
					)}
				</div>

				{this.state.loaded ? (
					<div className="dd4t3-settings-main">
						{this.state.currentTab === 'settings' && (
							<TabSettings settings={this.state.settings} />
						)}
						{this.state.currentTab === 'addons' && (
							<TabAddons addons={this.state.addons} />
						)}
						{this.state.currentTab === 'info' && <TabInfo />}
						{this.state.currentTab === 'general' && <TabGeneral />}
					</div>
				) : (
					<div className="dd4t3-settings-main">
						<Placeholder>
							<Spinner />
						</Placeholder>
					</div>
				)}
			</>
		)
	}
}
