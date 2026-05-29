import { __ } from '@wordpress/i18n'
import { DataViews } from '@wordpress/dataviews'
import { useCallback, useMemo, useState } from '@wordpress/element'
import { fields } from './fields'
import DeleteConfirmation from './delete-modal'
import EditRedirect from './edit-modal'
import { BulkActions } from '../../common'

const getItemId = (item) => String(item.id)

/**
 * Redirects DataViews table.
 *
 * State for view/items lives on the parent page so the page-level
 * "Add redirect" CTA can refetch after a successful create. Selection
 * is controlled here so {@see BulkActions} can portal a dropdown into
 * the native DataViews footer.
 */
const List = ({
	items,
	total,
	totalPages,
	isLoading,
	view,
	setView,
	updateRedirect,
	deleteRedirects,
}) => {
	const [editing, setEditing] = useState(null)
	const [selection, setSelection] = useState([])

	const clearSelection = useCallback(() => setSelection([]), [])

	const actions = useMemo(
		() => [
			{
				id: 'edit',
				label: __('Edit', '404-to-301'),
				callback: ([item]) => setEditing(item),
			},
			{
				id: 'toggle',
				label: __('Toggle active', '404-to-301'),
				callback: ([item]) =>
					updateRedirect(item.id, { is_active: !item.is_active }),
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
		[updateRedirect, deleteRedirects],
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
