import { __ } from '@wordpress/i18n'
import { applyFilters } from '@wordpress/hooks'
import { Button } from '@wordpress/components'
import { DataViews } from '@wordpress/dataviews'
import { useCallback, useMemo, useState } from '@wordpress/element'
import { published, search } from '@wordpress/icons'
import { fields } from './fields'
import { defaultView } from './view'
import DeleteConfirmation from './delete-modal'
import ViewDetails from './view-modal'
import ConfigureLog from './configure-modal'
import CustomRedirectModal from './custom-redirect-modal'
import useLogs from '../../hooks/use-logs'
import usePersistedView from '../../hooks/persisted-view'
import { BulkActions, EmptyState, isViewFiltered } from '../../common'

const STORAGE_KEY = '404_to_301_logs_view'
const getItemId = (item) => String(item.id)

/**
 * 404 logs DataViews table. Persists the user's view preferences in
 * localStorage so column visibility and density survive reloads.
 *
 * Selection is controlled so the {@see BulkActions} portal can render
 * its dropdown into the native footer container.
 */
const List = () => {
	const [view, setView] = usePersistedView(STORAGE_KEY, defaultView)
	const [selection, setSelection] = useState([])
	// "Custom redirect" launches the Redirects edit-modal — which
	// supplies its own <Modal> chrome. DataViews' `RenderModal` would
	// wrap that in a second modal, so we mount it from the page
	// instead, gated by `customRedirectLog`.
	const [customRedirectLog, setCustomRedirectLog] = useState(null)

	const {
		items,
		total,
		totalPages,
		isLoading,
		updateLog,
		bulkSetStatus,
		deleteLogs,
		refresh,
	} = useLogs(view)

	const clearSelection = useCallback(() => setSelection([]), [])

	// Empty-state handling — we render our own panel (see EmptyState) in
	// place of DataViews' bare "No results" line, with copy that depends
	// on whether the user has filtered the table down to nothing.
	const isEmpty = !isLoading && items.length === 0
	const isFiltered = isViewFiltered(view)
	const clearFilters = useCallback(
		() =>
			setView((prev) => ({ ...prev, search: '', filters: [], page: 1 })),
		[setView],
	)

	// Every log action lives inside the row's ⋮ dropdown menu — no
	// `isPrimary` / `icon` so nothing renders as an inline icon button
	// next to each row. The same actions also feed the BulkActions
	// dropdown when one or more rows are selected.
	//
	// Status callbacks read `rows` (could be a single row from the
	// per-row menu, or many from the bulk dropdown) and forward the
	// full id list to `bulkSetStatus`, which sends one REST request
	// regardless of the selection size. Same applies to delete via
	// `deleteLogs` — both endpoints accept bulk arrays now.
	const actions = useMemo(() => {
		const collectIds = (rows) => rows.map((row) => row.id)

		return [
			{
				// Single-row, read-only — opens the details modal.
				// `supportsBulk: false` keeps it out of the BulkActions
				// portal where it wouldn't make sense.
				id: 'view-details',
				label: __('View details', '404-to-301'),
				modalHeader: __('Log details', '404-to-301'),
				modalSize: 'large',
				supportsBulk: false,
				RenderModal: (props) => <ViewDetails {...props} />,
			},
			{
				// Per-row override toggles (Redirect / Log / Email).
				// Replaces the legacy plugin's per-log "Custom config"
				// pop-up — same Default / Enable / Disable semantics.
				id: 'configure',
				label: __('Configure', '404-to-301'),
				modalHeader: __('Configure log', '404-to-301'),
				supportsBulk: false,
				RenderModal: (props) => (
					<ConfigureLog {...props} onSave={updateLog} />
				),
			},
			{
				// Opens the Redirects "create" modal (seeded with the
				// log's path), creates the redirect, then links it
				// back to this log row — same flow as the legacy
				// plugin's "Custom redirect" inline editor.
				id: 'custom-redirect',
				label: __('Custom redirect', '404-to-301'),
				supportsBulk: false,
				callback: ([row]) => setCustomRedirectLog(row),
			},
			// Status mutations hide themselves when the row is already
			// in that status — keeps the row dropdown from listing
			// "Mark fixed" on a Fixed row, etc. For bulk selections
			// DataViews shows the action if at least one selected row
			// is eligible.
			{
				id: 'mark-fixed',
				label: __('Mark fixed', '404-to-301'),
				supportsBulk: true,
				isEligible: (item) => item.status !== 2,
				callback: (rows) => bulkSetStatus(collectIds(rows), 2),
			},
			{
				id: 'mark-ignored',
				label: __('Mark ignored', '404-to-301'),
				supportsBulk: true,
				isEligible: (item) => item.status !== 1,
				callback: (rows) => bulkSetStatus(collectIds(rows), 1),
			},
			{
				id: 'mark-open',
				label: __('Reopen', '404-to-301'),
				supportsBulk: true,
				isEligible: (item) => item.status !== 0,
				callback: (rows) => bulkSetStatus(collectIds(rows), 0),
			},
			{
				id: 'delete',
				// DataViews' per-row dropdown doesn't forward
				// `isDestructive` to the underlying Menu.Item, so the
				// label gets the colour treatment via its own class.
				// `modalHeader` covers the modal title (label isn't
				// used there). Both DataViews' menu and our bulk
				// dropdown render JSX labels via the
				// resolveActionLabel helper in `common/bulk-actions`.
				label: () => (
					<span className="d404-action-destructive">
						{__('Delete', '404-to-301')}
					</span>
				),
				modalHeader: __('Delete log', '404-to-301'),
				isDestructive: true,
				supportsBulk: true,
				RenderModal: (props) => (
					<DeleteConfirmation {...props} onConfirm={deleteLogs} />
				),
			},
		]
	}, [bulkSetStatus, deleteLogs, updateLog])

	// Addon slot for extra toolbar UI (eg. an Export-CSV button). Returns
	// `null` by default; the Logs Exporter addon swaps in its button.
	// `total` is the row count for the *current filtered view* (the
	// X-WP-Total header), so an addon can disable itself when the view
	// is empty; `isLoading` lets it wait out an in-flight fetch.
	const toolbar = applyFilters('d404.logs.toolbar', null, {
		view,
		selection,
		total,
		isLoading,
	})

	return (
		<>
			{toolbar}
			<DataViews
				data={items}
				fields={fields}
				view={view}
				onChangeView={setView}
				actions={actions}
				isLoading={isLoading}
				paginationInfo={{
					totalItems: total,
					totalPages,
				}}
				defaultLayouts={{ table: {} }}
				getItemId={getItemId}
				selection={selection}
				onChangeSelection={setSelection}
				config={{
					perPageSizes: [10, 25, 50, 100],
				}}
			/>

			{isEmpty &&
				(isFiltered ? (
					<EmptyState
						icon={search}
						title={__('No logs match your filters', '404-to-301')}
						description={__(
							'Try a different search or date range, or clear your filters to see every logged 404.',
							'404-to-301',
						)}
						action={
							<Button variant="secondary" onClick={clearFilters}>
								{__('Clear filters', '404-to-301')}
							</Button>
						}
					/>
				) : (
					<EmptyState
						icon={published}
						title={__('No 404 errors logged yet', '404-to-301')}
						description={__(
							"Nice — nothing's broken. When a visitor hits a missing URL, it'll show up here automatically.",
							'404-to-301',
						)}
					/>
				))}

			<BulkActions
				selection={selection}
				items={items}
				actions={actions}
				getItemId={getItemId}
				onClear={clearSelection}
			/>

			{customRedirectLog && (
				<CustomRedirectModal
					log={customRedirectLog}
					onClose={() => setCustomRedirectLog(null)}
					onSaved={refresh}
				/>
			)}
		</>
	)
}

export default List
