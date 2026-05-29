import { __, _n, sprintf } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { Button, Flex, FlexItem } from '@wordpress/components'

/**
 * Bulk / single delete confirmation modal for redirects.
 *
 * @param {Object}   props
 * @param {Object[]} props.items     Selected redirect rows.
 * @param {Function} props.closeModal Provided by DataViews.
 * @param {Function} props.onConfirm `(ids) => Promise<boolean>`
 */
const DeleteConfirmation = ({ items, closeModal, onConfirm }) => {
	const [isWorking, setIsWorking] = useState(false)
	const count = items.length

	const handleConfirm = async () => {
		setIsWorking(true)
		const ok = await onConfirm(items.map((item) => item.id))
		setIsWorking(false)
		if (ok && typeof closeModal === 'function') {
			closeModal()
		}
	}

	// DataViews wraps `RenderModal` content in its own <Modal>. Return
	// a fragment instead of nesting a second Modal (which would render
	// blank over the confirmation).
	return (
		<>
			<p>
				{sprintf(
					/* translators: %d: number of selected redirects. */
					_n(
						'Delete %d redirect? This cannot be undone.',
						'Delete %d redirects? This cannot be undone.',
						count,
						'404-to-301',
					),
					count,
				)}
			</p>

			{count === 1 && items[0]?.source && (
				<div className="d404-modal-subtitle">{items[0].source}</div>
			)}

			<Flex justify="flex-end" gap={2}>
				<FlexItem>
					<Button variant="tertiary" onClick={closeModal}>
						{__('Cancel', '404-to-301')}
					</Button>
				</FlexItem>
				<FlexItem>
					<Button
						variant="primary"
						isDestructive
						isBusy={isWorking}
						disabled={isWorking}
						onClick={handleConfirm}
					>
						{__('Delete', '404-to-301')}
					</Button>
				</FlexItem>
			</Flex>
		</>
	)
}

export default DeleteConfirmation
