const {Component} = wp.element
const {__} = wp.i18n
const {
	Button,
	PanelBody
} = wp.components

export default class TabInfo extends Component {
	constructor(props) {
		super(props);
		this.state = {}
	}

	render() {
		return (
			<>
				<PanelBody title={__('Help & Support', '404-to-301')}>
					<p>{__('Our extensive documentation contains references and guides for most situations you may encounter. We have an active and friendly community on our wp.org forum who may be able to help you figure out the ‘how-tos’ of the 404 to 301 world.', '404-to-301')}</p>
					<div className="dd4t3-button-group">
						<Button
							variant="secondary"
							target="_blank"
							icon="admin-page"
							href="https://duckdev.com/docs/404-to-301/"
						>
							{__('Documentation', '404-to-301')}
						</Button>

						<Button
							variant="secondary"
							target="_blank"
							icon="groups"
							href="https://wordpress.org/support/plugin/404-to-301/"
						>
							{__('Support Forums', '404-to-301')}
						</Button>
					</div>
				</PanelBody>

				<PanelBody title={__('About the Author', '404-to-301')}>
					<p>{__('Hey, I am Joel James, a WordPress developer from Kerala, India. I spend a few hours every week to contribute to open source. I have a few other WordPress plugin which I would recommend you to try out.', '404-to-301')}</p>
					<div className="dd4t3-button-group">
						<Button
							variant="secondary"
							target="_blank"
							icon="admin-site"
							href="https://duckdev.com/about/"
						>
							{__('About Me', '404-to-301')}
						</Button>

						<Button
							variant="secondary"
							target="_blank"
							icon="wordpress"
							href="https://profiles.wordpress.org/joelcj91/"
						>
							{__('WP.org Profile', '404-to-301')}
						</Button>
					</div>
				</PanelBody>
			</>
		);
	}
}