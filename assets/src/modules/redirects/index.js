import { __ } from '@wordpress/i18n'
import { Button } from '@wordpress/components'
import { useEffect, useState } from '@wordpress/element'
import { defaultView } from './view'
import EditRedirect from './edit-modal'
import List from './list'
import useRedirects from '../../hooks/use-redirects'
import usePersistedView from '../../hooks/persisted-view'
import { Notices, PageBody, PageHeader, SummaryCards } from '../../common'
import useSummary from '../../hooks/use-summary'

const STORAGE_KEY = '404_to_301_redirects_view'

/**
 * Read `?create=1&source=...&redirect_id=...&lock_source=1` params
 * injected by the Logs page when the user clicks "Custom redirect".
 */
const getUrlParams = () => {
	const params = new URLSearchParams(window.location.search)
	return {
		create: params.get('create') === '1',
		source: params.get('source') || '',
		redirectId: params.get('redirect_id')
			? parseInt(params.get('redirect_id'), 10)
			: null,
		lockSource: params.get('lock_source') === '1',
	}
}

/**
 * Redirects page — shell + DataViews list, plus the "Add redirect"
 * CTA in the header.
 */
const RedirectsPage = () => {
	const [view, setView] = usePersistedView(STORAGE_KEY, defaultView)
	const [isCreating, setIsCreating] = useState(false)
	const [editingRedirect, setEditingRedirect] = useState(null)
	const [lockSource, setLockSource] = useState(false)
	const [initialValues, setInitialValues] = useState(null)

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

	// On first render, check for URL params from the Logs page and
	// auto-open the appropriate modal.
	useEffect(() => {
		const { create, source, redirectId, lockSource: lock } = getUrlParams()

		if (lock && redirectId) {
			// Edit an existing redirect with source locked.
			setLockSource(true)
			// Find the redirect in the current items list — it may not be
			// loaded yet, so we poll items once they arrive.
		} else if (lock && create && source) {
			// Create a new redirect with source pre-filled and locked.
			setLockSource(true)
			setInitialValues({ source })
			setIsCreating(true)
		}
	}, []) // eslint-disable-line react-hooks/exhaustive-deps

	// When editing from a URL param (`redirect_id` in the query string),
	// wait for items to load then find and open the matching row.
	useEffect(() => {
		const { redirectId, lockSource: lock } = getUrlParams()
		if (!lock || !redirectId || isLoading || items.length === 0) {
			return
		}
		if (editingRedirect) {
			return
		}
		const found = items.find((item) => item.id === redirectId)
		if (found) {
			setEditingRedirect(found)
		}
	}, [items, isLoading]) // eslint-disable-line react-hooks/exhaustive-deps

	const cards = [
		{ label: __('Total', '404-to-301'), value: summary?.total ?? 0 },
		{ label: __('Active', '404-to-301'), value: summary?.active ?? 0 },
		{ label: __('Inactive', '404-to-301'), value: summary?.inactive ?? 0 },
		{ label: __('Total hits', '404-to-301'), value: summary?.hits ?? 0 },
	]

	const handleCloseModal = () => {
		setIsCreating(false)
		setEditingRedirect(null)
		setLockSource(false)
		setInitialValues(null)
	}

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

			{(isCreating || editingRedirect) && (
				<EditRedirect
					redirect={editingRedirect || null}
					initialValues={initialValues}
					lockSource={
						lockSource ||
						editingRedirect?.has_linked_log === true
					}
					onClose={handleCloseModal}
					onSave={async (data) =>
						editingRedirect
							? updateRedirect(editingRedirect.id, data)
							: createRedirect(data)
					}
				/>
			)}
		</>
	)
}

export default RedirectsPage
