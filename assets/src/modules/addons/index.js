import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { Placeholder, Spinner } from '@wordpress/components'
import useAddons from '../../hooks/use-addons'
import AddonCard from './addon-card'
import LicenseModal from './license-modal'
import { Notices, PageBody, PageHeader } from '../../common'

const AddonsApp = () => {
	const { items, isLoading, activateLicense, deactivateLicense } = useAddons()
	const [editing, setEditing] = useState(null)

	return (
		<>
			<PageHeader title={__('404 to 301 - Add-ons', '404-to-301')} />
			<PageBody wide>
				<Notices />

				<div className="d404-addons-page-body">
					{isLoading && items.length === 0 ? (
						<Placeholder>
							<Spinner />
						</Placeholder>
					) : (
						<div className="d404-addons-grid">
							{items.map((addon) => (
								<AddonCard
									key={addon.slug || addon.id}
									addon={addon}
									onManageLicense={(a) => setEditing(a)}
								/>
							))}
						</div>
					)}
				</div>
			</PageBody>

			{editing && (
				<LicenseModal
					addon={editing}
					onActivate={activateLicense}
					onDeactivate={deactivateLicense}
					onClose={() => setEditing(null)}
				/>
			)}
		</>
	)
}

export default AddonsApp
