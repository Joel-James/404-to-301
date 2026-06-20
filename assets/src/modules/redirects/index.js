import { __ } from '@wordpress/i18n'
import { Button } from '@wordpress/components'
import { useState } from '@wordpress/element'
import { defaultView } from './view'
import EditRedirect from './edit-modal'
import List from './list'
import useRedirects from '../../hooks/use-redirects'
import usePersistedView from '../../hooks/persisted-view'
import { Notices, PageBody, PageHeader, SummaryCards } from '../../common'
import useSummary from '../../hooks/use-summary'

const STORAGE_KEY = '404_to_301_redirects_view'

/**
 * Redirects page — shell + DataViews list, plus the "Add redirect"
 * CTA in the header.
 */
const RedirectsPage = () => {
	const [view, setView] = usePersistedView(STORAGE_KEY, defaultView)
	const [isCreating, setIsCreating] = useState(false)

	const {
		items,
		total,
		totalPages,
		isLoading,
		createRedirect,
		updateRedirect,
		deleteRedirects,
		bulkUpdateRedirects,
	} = useRedirects(view)

	const { data: summary, isLoading: summaryLoading } = useSummary(
		'/404-to-301/v1/redirects/summary',
	)

	const cards = [
		{ label: __('Total', '404-to-301'), value: summary?.total ?? 0 },
		{ label: __('Active', '404-to-301'), value: summary?.active ?? 0 },
		{ label: __('Inactive', '404-to-301'), value: summary?.inactive ?? 0 },
		{ label: __('Total hits', '404-to-301'), value: summary?.hits ?? 0 },
	]

	return (
		<>
			<PageHeader
				title={__('404 to 301 - Custom Redirects', '404-to-301')}
			/>
			<PageBody wide>
				<Notices />
				<SummaryCards cards={cards} isLoading={summaryLoading} />
				<div className="d404-list-actions">
					<Button
						variant="primary"
						icon="plus"
						onClick={() => setIsCreating(true)}
					>
						{__('Add redirect', '404-to-301')}
					</Button>
				</div>
				<List
					items={items}
					total={total}
					totalPages={totalPages}
					isLoading={isLoading}
					view={view}
					setView={setView}
					updateRedirect={updateRedirect}
					deleteRedirects={deleteRedirects}
					bulkUpdateRedirects={bulkUpdateRedirects}
				/>
			</PageBody>

			{isCreating && (
				<EditRedirect
					onClose={() => setIsCreating(false)}
					onSave={async (data) => createRedirect(data)}
				/>
			)}
		</>
	)
}

export default RedirectsPage
