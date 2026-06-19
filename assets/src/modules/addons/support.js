import { __ } from '@wordpress/i18n'
import { Button, Flex, FlexItem, PanelBody } from '@wordpress/components'

/**
 * Support links — top-level help resources for plugin users.
 *
 * Each row renders as a secondary button in `<LinkRow>`. Icons are
 * Dashicon slugs handed straight to `Button.icon`.
 */
const SUPPORT_LINKS = [
	{
		label: __('Documentation', '404-to-301'),
		icon: 'admin-page',
		href: 'https://docs.duckdev.com/404-to-301/',
	},
	{
		label: __('Support Forums', '404-to-301'),
		icon: 'groups',
		href: 'https://wordpress.org/support/plugin/404-to-301/',
	},
	{
		label: __('Priority Support', '404-to-301'),
		icon: 'superhero',
		href: 'https://duckdev.com/contact/',
	},
]

/**
 * Author / about links.
 *
 * Same shape as `SUPPORT_LINKS`; rendered in a second `<LinkRow>`
 * inside the "About the Author" panel.
 */
const AUTHOR_LINKS = [
	{
		href: 'https://duckdev.com/about/',
		icon: 'admin-site',
		label: __('About Us', '404-to-301'),
	},
	{
		href: 'https://profiles.wordpress.org/joelcj91/',
		icon: 'wordpress',
		label: __('WP.org Profile', '404-to-301'),
	},
]

/**
 * Horizontal row of action buttons used inside each PanelBody.
 *
 * @param {Object} props
 * @param {Array}  props.links Array of `{ label, icon, href }` objects.
 */
const LinkRow = ({ links }) => (
	<Flex className="d404-link-row" gap={2} justify="flex-start" wrap>
		{links.map((link) => (
			<FlexItem key={link.href}>
				<Button
					__next40pxDefaultSize
					variant="secondary"
					target="_blank"
					rel="noopener noreferrer"
					icon={link.icon}
					href={link.href}
				>
					{link.label}
				</Button>
			</FlexItem>
		))}
	</Flex>
)

/**
 * Support tab.
 *
 * Two stacked `PanelBody` blocks — one for documentation / forums /
 * priority support, one for the author info. The layout mirrors the
 * LLC plugin's Support tab so users moving between DuckDev plugins
 * get a familiar shape.
 */
const Support = () => (
	<>
		<PanelBody title={__('Support Information', '404-to-301')}>
			<p>
				{__(
					'Access our detailed documentation to handle most situations. For human feedback or community help, visit our wp.org forum. Premium customers receive priority support by contacting us directly.',
					'404-to-301',
				)}
			</p>
			<LinkRow links={SUPPORT_LINKS} />
		</PanelBody>

		<PanelBody title={__('About the Author', '404-to-301')}>
			<p>
				{__(
					"Hey, I'm Joel James, a Software Engineer based in Kerala, India. I'm passionate about open source and dedicate a lot of my time to contributing to it. If you like this plugin, I'd encourage you to check out my other WordPress plugins as well!",
					'404-to-301',
				)}
			</p>
			<LinkRow links={AUTHOR_LINKS} />
		</PanelBody>
	</>
)

export default Support
