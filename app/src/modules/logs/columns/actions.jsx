/* global wp */
import React, { useState } from 'react'
import DeleteModal from './actions/delete-modal'
import RedirectModal from './actions/redirect-modal'
import ConfigureModal from './actions/configure-modal'
import { Button, Dashicon } from '@wordpress/components'
import { BodyColumn } from '@/components/table/table'

const { __ } = wp.i18n

const ActionsColumn = ({ log, onUpdate, onDelete }) => {
	const [showConfig, setShowConfig] = useState(false)
	const [showDelete, setShowDelete] = useState(false)
	const [showRedirect, setShowRedirect] = useState(false)

	return (
		<BodyColumn id="actions">
			<Button
				className="redirectpress-logs-tooltip"
				data-tooltip={__('Setup custom redirect', '404-to-301')}
				variant="tertiary"
				onClick={() => setShowRedirect(true)}
			>
				<Dashicon icon="external" />
			</Button>
			<Button
				variant="tertiary"
				className="redirectpress-logs-tooltip"
				data-tooltip={__('Configure actions', '404-to-301')}
				onClick={() => setShowConfig(true)}
			>
				<Dashicon icon="admin-tools" />
			</Button>
			<Button
				variant="tertiary"
				className="redirectpress-logs-tooltip"
				data-tooltip={__('Delete log', '404-to-301')}
				isDestructive={true}
				onClick={() => setShowDelete(true)}
			>
				<Dashicon icon="trash" />
			</Button>
			{showConfig && (
				<ConfigureModal
					log={log}
					onClose={() => setShowConfig(false)}
					onSave={(log) => {
						onUpdate(log)
						setShowConfig(false)
					}}
				/>
			)}
			{showDelete && (
				<DeleteModal
					log={log}
					onClose={() => setShowDelete(false)}
					onDelete={(log) => {
						onDelete(log)
						setShowDelete(false)
					}}
				/>
			)}
			{showRedirect && (
				<RedirectModal
					log={log}
					onClose={() => setShowRedirect(false)}
					onSave={(log) => {
						onUpdate(log)
						setShowRedirect(false)
					}}
				/>
			)}
		</BodyColumn>
	)
}

export default ActionsColumn
