/* global dd4t3 */
const {__} = wp.i18n
const {Component} = wp.element
const {
	Spinner,
	Placeholder,
} = wp.components

import request from './helpers/request'

import TabInfo from './settings/tab-info'
import TabAddons from './settings/tab-addons'
import TabSettings from './settings/tab-settings'
import NavTabs from './settings/components/nav-tabs'
import { ReactNotifications } from 'react-notifications-component'

export default class Settings extends Component {
	constructor(props) {
		super(props);

		this.state = {
			addons: [],
			settings: {},
			loaded: false,
			currentTab: 'settings',
		};
	}

	async componentDidMount() {
		const self = this

		await Promise.all([
			// Get the list of addons.
			request.get('/data/addons')
			.then(function (response) {
				self.setState({addons: response.data.data})
			}),
			// Get the list of addons.
			request.get('/settings')
			.then(function (response) {
				self.setState({settings: response.data.data})
			})
		])

		// Data is loaded.
		self.setState({loaded: true})
	}

	render() {
		return (
			<>
				<ReactNotifications />
				<div className="dd4t3-header">
					<div className="dd4t3-title-section">
						<h1>404 to 301</h1>
						<abbr title={'Version: ' + dd4t3.version} className="version">
							v{dd4t3.version}
						</abbr>
					</div>

					{this.state.loaded &&
						<NavTabs
							current={this.state.currentTab}
							navs={{
								settings: __('Settings', '404-to-301'),
								addons: __('Add-ons', '404-to-301'),
								info: __('Info', '404-to-301')
							}}
							onChange={(tab) => {
								this.setState({currentTab: tab})
							}}
						/>
					}

				</div>

				{this.state.loaded ?
					<div className="dd4t3-main">
						{this.state.currentTab === 'settings' && <TabSettings settings={this.state.settings}/>}
						{this.state.currentTab === 'addons' && <TabAddons addons={this.state.addons}/>}
						{this.state.currentTab === 'info' && <TabInfo/>}
					</div>
					:
					<div className="dd4t3-main">
						<Placeholder>
							<Spinner/>
						</Placeholder>
					</div>
				}
			</>
		);
	}
}