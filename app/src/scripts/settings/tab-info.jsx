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
			<PanelBody title={__('Help & Support', '404-to-301')}>
				<div className="dd4t3-info">
					<p>{__('Our extensive documentation contains references and guides for most situations you may encounter. We have an active and friendly community on our wp.org forum who may be able to help you figure out the ‘how-tos’ of the 404 to 301 world.', '404-to-301')}</p>
					<p>{__('If the above options did not help you can reach out to me directly through my contact form. Please note, it may take a few days for me to reply as I am the only developer working on new features, bug fixes, improvements, documentation, customer support etc. after my full time job.', '404-to-301')}</p>
					<div className="dd4t3-info-button-group">
						<Button
							variant="secondary"
							target="_blank"
							icon="external"
							href="https://duckdev.com/docs/404-to-301/"
						>
							{__('Documentation', '404-to-301')}
						</Button>

						<Button
							variant="secondary"
							target="_blank"
							icon="external"
							href="https://wordpress.org/support/plugin/404-to-301/"
						>
							{__('Support Forums', '404-to-301')}
						</Button>
					</div>
				</div>
			</PanelBody>
		);
	}
}