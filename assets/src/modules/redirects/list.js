import { __ } from '@wordpress/i18n'
import { Button } from '@wordpress/components'
import { DataViews } from '@wordpress/dataviews'
import { useCallback, useMemo, useState } from '@wordpress/element'
import { arrowRight, search } from '@wordpress/icons'
import { fields } from './fields'
import DeleteConfirmation from './delete-modal'
import EditRedirect from './edit-modal'
import { BulkActions, EmptyState, isViewFiltered } from '../../common'

const getItemId = (item) => String(item.id)

/**
 * Redirects DataViews table.
 *
 * State for view/items lives on the parent page so the page-level
 * "Add redirect" CTA can refetch after a successful create. Selection
 * is controlled here so {@see BulkActions} can portal a dropdown into
 * the native DataViews footer.
 */
const collectIds = (rows) => rows.map((r) => r.id)

const List = ({
	items,
	total,
	totalPages,
	isLoading,
	view,
	setView,
	updateRedirect,
	deleteRedirects,
	bulkUpdateRedirects,
}) => {
	const [editing, setEditing] = useState(null)
	const [selection, setSelection] = useState([])

	const clearSelection = useCallback(() => setSelection([]), [])

	// Our own empty-state panel replaces DataViews' bare "No results"
	// line; copy depends on whether the table is filtered down to zero.
	const isEmpty = !isLoading && items.length === 0
	const isFiltered = isViewFiltered(view)
	const clearFilters = useCallback(
		() =>
			setView((prev) => ({ ...prev, search: '', filters: [], page: 1 })),
		[setView],
	)

	const actions = useMemo(
		() => [
			{
				id: 'edit',
				label: __('Edit', '404-to-301'),
				supportsBulk: false,
				callback: ([item]) => setEditing(item),
			},
			// Activate / Deactivate hide themselves when the row is
			// already in that state — an active redirect only offers
			// "Deactivate", and vice versa. For bulk selections DataViews
			// keeps the action if at least one selected row is eligible.
			// This replaces the old standalone "Toggle active" action,
			// which duplicated these two.
			{
				id: 'activate',
				label: __('Activate', '404-to-301'),
				supportsBulk: true,
				isEligible: (item) => !item.is_active,
				callback: (rows) =>
					bulkUpdateRedirects(collectIds(rows), { is_active: true }),
			},
			{
				id: 'deactivate',
				label: __('Deactivate', '404-to-301'),
				supportsBulk: true,
				isEligible: (item) => item.is_active,
				callback: (rows) =>
					bulkUpdateRedirects(collectIds(rows), { is_active: false }),
			},
			{
				id: 'delete',
				// JSX label so the menu item picks up its red colour —
				// DataViews' per-row dropdown ignores `isDestructive`.
				// See the matching action on the Logs list for context.
				label: () => (
					<span className="d404-action-destructive">
						{__('Delete', '404-to-301')}
					</span>
				),
				modalHeader: __('Delete redirects', '404-to-301'),
				isDestructive: true,
				supportsBulk: true,
				RenderModal: (props) => (
					<DeleteConfirmation
						{...props}
						onConfirm={deleteRedirects}
					/>
				),
			},
		],
		[deleteRedirects, bulkUpdateRedirects],
	)

	return (
		<>
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
						title={__(
							'No redirects match your filters',
							'404-to-301',
						)}
						description={__(
							'Try a different search, or clear your filters to see all your redirects.',
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
						icon={arrowRight}
						title={__('No redirects yet', '404-to-301')}
						description={__(
							'Use "Add redirect" above to send an old or broken URL to a new destination.',
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

			{editing && (
				<EditRedirect
					redirect={editing}
					onClose={() => setEditing(null)}
					onSave={(data) => updateRedirect(editing.id, data)}
				/>
			)}
		</>
	)
}

export default List
