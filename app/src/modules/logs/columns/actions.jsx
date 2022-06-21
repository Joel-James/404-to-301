/* global wp */
import React, { useState } from 'react'
import {
	Dashicon,
	__experimentalConfirmDialog as ConfirmDialog,
	Button,
	Modal,
	Flex,
	TextControl,
} from '@wordpress/components'
import BodyColumn from '@/components/table/columns/body-column'
import EditModal from './actions/edit-modal'

const { __ } = wp.i18n

const ActionsColumn = ({ log }) => {
	const [isDeleteOpen, setIsDeleteOpen] = useState(false)
	const [isEditOpen, setEditOpen] = useState(false)
	const [isRedirectOpen, setRedirectOpen] = useState(false)
	const [customRedirectUrl, setCustomRedirectUrl] = useState('')

	const openRedirectModal = () => setRedirectOpen(true)
	const closeRedirectModal = () => setRedirectOpen(false)

	const handleDeleteConfirm = () => setIsDeleteOpen(false)
	const handleDeleteCancel = () => setIsDeleteOpen(false)

	return (
		<BodyColumn id="actions">
			<a
				href="javascript:void(0)"
				title="Setup custom redirect"
				onClick={openRedirectModal}
			>
				<Dashicon icon="external" />
			</a>
			<a
				href="javascript:void(0)"
				title="Edit log actions"
				onClick={() => setEditOpen(true)}
			>
				<Dashicon icon="admin-tools" />
			</a>
			<a
				href="javascript:void(0)"
				title="Delete log"
				onClick={() => setIsDeleteOpen(!isDeleteOpen)}
			>
				<Dashicon icon="trash" className="dd4t3-logs-red" />
			</a>
			{isEditOpen && (
				<EditModal
					log={log}
					onClose={() => setEditOpen(false)}
					onSave={() => setEditOpen(false)}
				/>
			)}
			{isRedirectOpen && (
				<Modal
					title={__('Custom Redirect', '404-to-301')}
					onRequestClose={closeRedirectModal}
				>
					<TextControl
						label={__('Custom URL', '404-to-301')}
						help={__(
							'Enter a custom URL you would like this to get redirected.',
							'404-to-301'
						)}
						type="url"
						value={customRedirectUrl}
						onChange={(value) => setCustomRedirectUrl(value)}
					/>
					<Flex direction="row" justify="flex-end">
						<Button
							variant="secondary"
							onClick={closeRedirectModal}
						>
							{__('Cancel', '404-to-301')}
						</Button>
						<Button variant="primary" onClick={closeRedirectModal}>
							{__('Save Changes', '404-to-301')}
						</Button>
					</Flex>
				</Modal>
			)}
			<ConfirmDialog
				isOpen={isDeleteOpen}
				onConfirm={handleDeleteConfirm}
				onCancel={handleDeleteCancel}
				confirmButtonText={__('Delete', '404-to-301')}
			>
				<strong>{__('Delete error log?', '404-to-301')}</strong>
				<br />
				<br />
				{__(
					"Custom redirect (if any) won't be deleted along with log.",
					'404-to-301'
				)}
			</ConfirmDialog>
		</BodyColumn>
	)
}

export default ActionsColumn
