import { __ } from '@wordpress/i18n'
import {
	Button,
	Flex,
	FlexItem,
	Notice,
	Spinner,
} from '@wordpress/components'
import { useState } from '@wordpress/element'
import { update as updateIcon } from '@wordpress/icons'
import useAddons from '../../hooks/use-addons'
import AddonCard from './addon-card'
import LicenseModal from './license-modal'

/**
 * Catalog tab body.
 *
 * Pulled out of the page-level component so each tab owns its data
 * and effects. The tab is mounted lazily by the parent (it's the
 * default tab), and the `useAddons` hook lives here so unmounting
 * the tab also tears down its fetches and timers.
 *
 * Layout:
 *   - Toolbar with a primary "Refresh" button that bypasses the
 *     server-side Freemius cache.
 *   - Responsive grid of `<AddonCard>` components, or an empty
 *     state when the catalog comes back empty.
 *   - One `<LicenseModal>` hoisted to the tab root so it can sit
 *     above any card and keep focus management clean.
 */
const Catalog = () => {
	const {
		items,
		isLoading,
		isRefreshing,
		refresh,
		activateLicense,
		deactivateLicense,
	} = useAddons()

	// Currently-managed addon id (or null when the modal is closed).
	// We read the live row from `items` rather than capturing it in
	// state so a successful (de)activation patches the modal label
	// without forcing it to re-open.
	const [managingId, setManagingId] = useState(null)
	const managingAddon =
		managingId !== null
			? items.find((addon) => Number(addon.id) === Number(managingId))
			: null

	return (
		<>
			<Flex
				justify="flex-end"
				align="center"
				className="d404-addons-toolbar"
			>
				<FlexItem>
					<Button
						variant="secondary"
						icon={updateIcon}
						onClick={refresh}
						isBusy={isRefreshing}
						disabled={isRefreshing || isLoading}
					>
						{isRefreshing
							? __('Refreshing…', '404-to-301')
							: __('Refresh', '404-to-301')}
					</Button>
				</FlexItem>
			</Flex>

			{isLoading && items.length === 0 ? (
				<div className="d404-page-loader">
					<Spinner />
				</div>
			) : items.length === 0 ? (
				<Notice status="info" isDismissible={false}>
					{__(
						'No add-ons available yet. Try the Refresh button above or check back later.',
						'404-to-301',
					)}
				</Notice>
			) : (
				<div className="d404-addons-grid">
					{items.map((addon) => (
						<AddonCard
							key={addon.id}
							addon={addon}
							onManageLicense={(target) =>
								setManagingId(target.id)
							}
						/>
					))}
				</div>
			)}

			{managingAddon && (
				<LicenseModal
					addon={managingAddon}
					onActivate={activateLicense}
					onDeactivate={deactivateLicense}
					onClose={() => setManagingId(null)}
				/>
			)}
		</>
	)
}

export default Catalog
