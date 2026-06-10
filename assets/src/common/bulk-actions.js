import { __ } from '@wordpress/i18n'
import { useLayoutEffect, useRef, useState } from '@wordpress/element'
import { createPortal } from 'react-dom'
import { DropdownMenu, MenuGroup, MenuItem, Modal } from '@wordpress/components'
import { cog } from '@wordpress/icons'

/**
 * Bulk-actions dropdown.
 *
 * Renders **only the dropdown** — the "N items selected" indicator and
 * the close button next to it are already provided natively by
 * DataViews inside `.dataviews-bulk-actions-footer__container`. We
 * portal our dropdown into that same container so it sits between
 * the native indicator and the pagination on the right.
 *
 * @param {Object}        props
 * @param {string[]}      props.selection Selected item ids (DataViews uses string ids).
 * @param {Array<Object>} props.items     Current page rows (used to resolve selection -> row data).
 * @param {Array<Object>} props.actions   Same action array passed to DataViews.
 * @param {Function}      [props.getItemId] `(item) => string` — must match the DataViews prop.
 * @param {Function}      [props.onClear]   Called after a callback-action runs so the selection clears.
 *
 * @return {JSX.Element|null}
 */
const BulkActions = ({
	selection = [],
	items = [],
	actions = [],
	getItemId = (item) => String(item.id),
	onClear,
}) => {
	const [target, setTarget] = useState(null)
	const [activeModal, setActiveModal] = useState(null)

	// Keep the latest snapshots in refs so the menu handlers don't
	// capture stale data through closure.
	const itemsRef = useRef(items)
	itemsRef.current = items
	const selectionRef = useRef(selection)
	selectionRef.current = selection

	// Look for the native bulk-actions container the moment a
	// selection appears. DataViews mounts the footer lazily.
	useLayoutEffect(() => {
		if (selection.length === 0) {
			setTarget(null)
			return
		}

		const el = document.querySelector(
			'.dataviews-wrapper .dataviews-bulk-actions-footer__container',
		)
		setTarget(el || null)
	}, [selection])

	if (selection.length === 0 || !target) {
		return null
	}

	const resolveSelectedItems = () => {
		const ids = new Set(selectionRef.current.map(String))
		return itemsRef.current.filter((item) => ids.has(getItemId(item)))
	}

	// Items that pass an action's isEligible — defaults to all selected
	// items when the action doesn't define one. Matches DataViews'
	// per-row dropdown: an action shows only when at least one row
	// qualifies, and the callback receives only those rows.
	const eligibleItemsFor = (action) => {
		const selected = resolveSelectedItems()
		if (typeof action.isEligible !== 'function') {
			return selected
		}
		return selected.filter((item) => action.isEligible(item))
	}

	const bulkActions = actions.filter(
		(a) => a.supportsBulk && eligibleItemsFor(a).length > 0,
	)
	if (bulkActions.length === 0) {
		return null
	}

	const handleAction = (action, onClose) => {
		onClose()

		if (action.RenderModal) {
			setActiveModal(action)
			return
		}

		if (typeof action.callback === 'function') {
			action.callback(eligibleItemsFor(action))
			if (typeof onClear === 'function') {
				onClear()
			}
		}
	}

	const closeModal = () => {
		setActiveModal(null)
		if (typeof onClear === 'function') {
			onClear()
		}
	}

	const ActiveModal = activeModal?.RenderModal

	// Dropdown portals into the footer container so it sits next to the
	// native "N selected" indicator.
	const dropdown = createPortal(
		<DropdownMenu
			icon={cog}
			label={__('Bulk actions', '404-to-301')}
			toggleProps={{
				/* Match the filter button's icon-only look. The
				 * `label` above doubles as the accessible name and
				 * the hover tooltip. */
				size: 'small',
				showTooltip: true,
				tooltipPosition: 'top center',
			}}
			popoverProps={{ placement: 'top-start' }}
			className="d404-bulk-actions"
		>
			{({ onClose }) => (
				<MenuGroup>
					{bulkActions.map((action) => (
						<MenuItem
							key={action.id}
							isDestructive={action.isDestructive}
							onClick={() => handleAction(action, onClose)}
						>
							{/* Match DataViews' contract: a label
							 * can be a string or a function that
							 * receives the current selection. */}
							{typeof action.label === 'function'
								? action.label(resolveSelectedItems())
								: action.label}
						</MenuItem>
					))}
				</MenuGroup>
			)}
		</DropdownMenu>,
		target,
	)

	// Modal renders OUTSIDE the footer portal. `<Modal>` from
	// @wordpress/components already portals itself to <body>, draws
	// its own overlay, traps focus, and closes on escape — nesting
	// it inside the footer container clipped the overlay to the
	// footer bar and rendered the confirmation as bare text. The
	// action components (delete-modal etc.) intentionally don't
	// wrap themselves in `<Modal>` — they expect the host
	// (DataViews, or this component now) to supply the chrome.
	return (
		<>
			{dropdown}
			{ActiveModal && (
				<Modal
					title={
						activeModal.modalHeader ||
						(typeof activeModal.label === 'string'
							? activeModal.label
							: '')
					}
					onRequestClose={closeModal}
					className="d404-bulk-actions-modal"
				>
					<ActiveModal
						items={eligibleItemsFor(activeModal)}
						closeModal={closeModal}
					/>
				</Modal>
			)}
		</>
	)
}

export default BulkActions
