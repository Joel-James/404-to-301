import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import Catalog from './catalog'
import Support from './support'
import { Notices, PageBody, PageHeader, TabNav } from '../../common'

/**
 * Tab registry for the Add-ons page.
 *
 * Same shape as the settings-tabs registry — key, label, component.
 * Adding a new tab is a one-entry change here; the rest of the
 * component is data-driven.
 *
 * The Support tab content is borrowed from the LLC plugin's Support
 * panel pattern (two PanelBody blocks, link rows of secondary
 * buttons) but with 404-to-301 URLs.
 */
const tabs = {
	catalog: {
		label: __('Add-ons', '404-to-301'),
		component: Catalog,
	},
	support: {
		label: __('Support', '404-to-301'),
		component: Support,
	},
}

/**
 * Root component for the Addons admin page.
 *
 * Hosts the tab nav inside the page header (same chrome the Settings
 * page uses) and renders the active tab's body inside the standard
 * `<PageBody>`. The body wrapper is *not* set to `wide` for the
 * Support tab — its content sits comfortably in the 780px column —
 * but the Catalog tab opts into the wider responsive container so
 * the addon grid can spread out on big monitors.
 *
 * Tab state lives here so switching tabs unmounts the previous tab
 * (and therefore tears down its hooks / network requests).
 */
const AddonsApp = () => {
	const tabKeys = Object.keys(tabs)
	const [current, setCurrent] = useState(tabKeys[0])

	const navs = Object.fromEntries(
		Object.entries(tabs).map(([key, tab]) => [key, tab.label]),
	)

	const ActiveTab = (tabs[current] || tabs[tabKeys[0]]).component

	// Catalog needs the wide responsive layout so the addon grid can
	// spread out; Support fits comfortably in the narrow Settings
	// column.
	const isWide = current === 'catalog'

	return (
		<>
			<PageHeader title={__('404 to 301 - Add-ons', '404-to-301')}>
				<TabNav current={current} navs={navs} onChange={setCurrent} />
			</PageHeader>

			<PageBody wide={isWide}>
				<Notices />
				<ActiveTab />
			</PageBody>
		</>
	)
}

export default AddonsApp
