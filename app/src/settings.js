/* eslint-disable camelcase */
/**
 * WordPress dependencies
 */
const {__} = wp.i18n;

const {
	Placeholder,
	Spinner,
} = wp.components;

const {
	render,
	Component,
	Fragment
} = wp.element

const axios = require('axios');
const axiosConfig = {
	baseURL: dd4t3.rest.base,
	headers: {
		'X-WP-Nonce': dd4t3.rest.nonce,
	},
};

// Create new axios instance.
const request = axios.create(axiosConfig)

import Info from './scripts/tabs/info'
import Addons from './scripts/tabs/addons'
import Settings from './scripts/tabs/settings'
import NavTabs from './scripts/components/nav-tabs'

import './styles/style.scss'

class App extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			currentTab: 'settings',
			isAPILoaded: false,
			isAPISaving: false,
			addons: [],
			settings: {}
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

		// API is loaded.
		self.setState({isAPILoaded: true})
	}

	render() {
		return (
			<Fragment>
				<div className="dd4t3-header">
					<div className="dd4t3-title-section">
						<h1>
							{__('404 to 301')}
						</h1>
						<abbr title="Version: 1.7.4" className="version">v1.7.4</abbr>
					</div>

					{this.state.isAPILoaded &&
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

				{
					!this.state.isAPILoaded ?
						<div className="dd4t3-main">
							<Placeholder>
								<Spinner/>
							</Placeholder>
						</div>
						:
						<div className="dd4t3-main">
							{this.state.currentTab === 'settings' && <Settings settings={this.state.settings}/>}
							{this.state.currentTab === 'addons' && <Addons addons={this.state.addons}/>}
							{this.state.currentTab === 'info' && <Info/>}
						</div>
				}
			</Fragment>
		);
	}
}

render(
	<App/>,
	document.getElementById('dd4t3-settings-app')
);
