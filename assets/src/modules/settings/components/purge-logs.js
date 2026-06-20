import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import { Button, Flex, FlexItem, Modal } from '@wordpress/components'
import apiFetch from '@wordpress/api-fetch'

/**
 * "Danger Zone" panel for the Tools settings tab.
 *
 * Renders a "Purge all logs" button. Clicking it opens a confirmation
 * modal before issuing DELETE /404-to-301/v1/logs/purge. Custom
 * redirects live in a separate table and are never touched.
 */
const PurgeLogs = () => {
	const [isModalOpen, setIsModalOpen] = useState(false)
	const [isPurging, setIsPurging] = useState(false)
	const [notice, setNotice] = useState(null) // { type: 'success'|'error', message }

	const openModal = () => {
		setNotice(null)
		setIsModalOpen(true)
	}
	const closeModal = () => setIsModalOpen(false)

	const handlePurge = async () => {
		setIsPurging(true)
		try {
			await apiFetch({
				path: '/404-to-301/v1/logs/purge',
				method: 'DELETE',
			})
			setNotice({
				type: 'success',
				message: __('All logs have been deleted.', '404-to-301'),
			})
		} catch {
			setNotice({
				type: 'error',
				message: __(
					'Could not purge logs. Please try again.',
					'404-to-301',
				),
			})
		} finally {
			setIsPurging(false)
			setIsModalOpen(false)
		}
	}

	return (
		<div className="d404-purge-wrap">
			{notice && (
				<div className={`d404-purge-notice d404-purge-notice--${notice.type}`}>
					{notice.message}
				</div>
			)}

			<p className="d404-purge-description">
				{__(
					'Permanently delete every entry in the 404 error log. This action cannot be undone. Custom redirects are not affected.',
					'404-to-301',
				)}
			</p>

			<Button variant="primary" isDestructive onClick={openModal}>
				{__('Purge all logs', '404-to-301')}
			</Button>

			{isModalOpen && (
				<Modal
					title={__('Purge all logs?', '404-to-301')}
					onRequestClose={closeModal}
					size="small"
				>
					<p>
						{__(
							'This will permanently delete every entry in the 404 error log. This action cannot be undone.',
							'404-to-301',
						)}
					</p>
					<p>
						{__(
							'Custom redirects will not be affected.',
							'404-to-301',
						)}
					</p>

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
								isBusy={isPurging}
								disabled={isPurging}
								onClick={handlePurge}
							>
								{__('Yes, purge all logs', '404-to-301')}
							</Button>
						</FlexItem>
					</Flex>
				</Modal>
			)}
		</div>
	)
}

export default PurgeLogs
