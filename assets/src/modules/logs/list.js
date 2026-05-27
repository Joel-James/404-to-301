import { __ } from '@wordpress/i18n'
import { DataViews } from '@wordpress/dataviews'
import { useCallback, useMemo, useState } from '@wordpress/element'
import { fields } from './fields'
import { defaultView } from './view'
import DeleteConfirmation from './delete-modal'
import useLogs from '../../hooks/use-logs'
import usePersistedView from '../../hooks/persisted-view'
import { BulkActions } from '../../common'

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

	const { items, total, totalPages, isLoading, updateLog, deleteLogs } =
		useLogs(view)

	const clearSelection = useCallback(() => setSelection([]), [])

	// Every log action lives inside the row's ⋮ dropdown menu — no
	// `isPrimary` / `icon` so nothing renders as an inline icon button
	// next to each row. The same actions also feed the BulkActions
	// dropdown when one or more rows are selected.
	const actions = useMemo(
		() => [
			{
				id: 'mark-fixed',
				label: __('Mark fixed', '404-to-301'),
				supportsBulk: true,
				callback: (rows) =>
					rows.forEach((row) => updateLog(row.id, { status: 2 })),
			},
			{
				id: 'mark-ignored',
				label: __('Mark ignored', '404-to-301'),
				supportsBulk: true,
				callback: (rows) =>
					rows.forEach((row) => updateLog(row.id, { status: 1 })),
			},
			{
				id: 'mark-open',
				label: __('Reopen', '404-to-301'),
				supportsBulk: true,
				callback: (rows) =>
					rows.forEach((row) => updateLog(row.id, { status: 0 })),
			},
			{
				id: 'delete',
				label: __('Delete', '404-to-301'),
				isDestructive: true,
				supportsBulk: true,
				RenderModal: (props) => (
					<DeleteConfirmation {...props} onConfirm={deleteLogs} />
				),
			},
		],
		[updateLog, deleteLogs],
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

			<BulkActions
				selection={selection}
				items={items}
				actions={actions}
				getItemId={getItemId}
				onClear={clearSelection}
			/>
		</>
	)
}

export default List
