/* global wp */
import React from 'react'
import {
	Flex,
	Modal,
	Button,
	__experimentalText as Text,
	__experimentalVStack as VStack,
} from '@wordpress/components'
import notify from '@/helpers/notify'
import request from '@/helpers/request'

const { __ } = wp.i18n

export default class DeleteeModal extends React.Component {
	constructor(props) {
		super(props)

		this.state = {
			deleting: false,
		}
	}

	/**
	 * Delete current log using API.
	 *
	 * @since 4.0.0
	 */
	async deleteLog() {
		// Make progress button.
		this.setState({ deleting: true })

		// Get the list of addons.
		await request
			.delete('/logs/' + this.props.log.log_id)
			.then(function () {
				// Show notification.
				notify(__('Log deleted.', '404-to-301'))
			})
			.catch(function () {
				// Show notification.
				notify(
					__('Delete failed. Please try again.', '404-to-301'),
					'error'
				)
			})

		// Remove progress button.
		this.setState({ deleting: false })

		// Trigger save event.
		this.props.onDelete()
	}

	render() {
		return (
			<Modal onRequestClose={this.props.onClose} __experimentalHideHeader>
				<VStack spacing={5}>
					<Text>
						<strong>{__('Delete error log?', '404-to-301')}</strong>
					</Text>
					<Text>
						{__(
							"If you have a custom redirect created for this log, it won't be deleted.",
							'404-to-301'
						)}
					</Text>
					<Flex direction="row" justify="flex-end">
						<Button variant="tertiary" onClick={this.props.onClose}>
							{__('Cancel', '404-to-301')}
						</Button>
						<Button
							variant="primary"
							isDestructive={!this.state.deleting}
							disabled={this.state.deleting}
							isBusy={this.state.deleting}
							onClick={() => this.deleteLog()}
						>
							{this.state.deleting
								? __('Deleting..', '404-to-301')
								: __('Delete', '404-to-301')}
						</Button>
					</Flex>
				</VStack>
			</Modal>
		)
	}
}
