/* global wp */
import React, { useState } from 'react'
import classNames from 'classnames'
import {
	Card,
	Dashicon,
	Button,
	Notice,
	Modal,
	CardBody,
	CardMedia,
	CardHeader,
	CardFooter,
	ToggleControl,
	__experimentalGrid,
	__experimentalText,
	__experimentalHeading,
} from '@wordpress/components'

const { __ } = wp.i18n

const LogsCleaner = () => {
	return (
		<div className="joel">
			<ToggleControl
				checked={true}
				label={__(
					'Enable email notifications for 404 errors',
					'404-to-301'
				)}
				help={__(
					'Do you want to receive and email notification for each 404 errors? Then please configure it belo',
					'404-to-301'
				)}
				onChange={(checked) => window.console.log(checked)}
			/>
		</div>
	)
}

const LogsExporter = () => {
	return <div className="joel">This is logs exporter content</div>
}

wp.hooks.addFilter('dd404_addon_modal', 'namespace', function (content, id) {
	if ('logs-exporter' === id) {
		return <LogsExporter />
	} else if ('logs-cleaner' === id) {
		return <LogsCleaner />
	}

	return content
})

export default function TabAddons(props) {
	const [isConfigOpen, setConfigOpen] = useState(false)
	const [configAddon, setConfigAddon] = useState('')

	const openConfigModal = (id) => {
		setConfigAddon(id)
		setConfigOpen(true)
	}

	return (
		<>
			{props.addons.length <= 0 ? (
				<Notice status="info" isDismissible={false}>
					<p>{__('No addons found.', '404-to-301')}</p>
				</Notice>
			) : (
				<__experimentalGrid columns={2}>
					{props.addons.map((addon) => (
						<Card key={addon.title}>
							<CardHeader>
								<__experimentalHeading level={5}>
									{addon.title}
								</__experimentalHeading>
								{addon.is_installed && (
									<Dashicon
										icon="yes-alt"
										title={
											addon.is_active
												? __('Active', '404-to-301')
												: __('Installed', '404-to-301')
										}
										className={classNames({
											'dd4t3-settings-green':
												addon.is_active,
											'dd4t3-settings-grey':
												!addon.is_active,
										})}
									/>
								)}
							</CardHeader>
							<CardMedia>
								<img src="https://kinsta.com/wp-content/uploads/2015/08/wordpress-error-log-3.png" />
							</CardMedia>
							<CardBody>
								<__experimentalText>
									{addon.description}
								</__experimentalText>
							</CardBody>
							<CardFooter>
								{addon.is_active ? (
									<Button
										variant="primary"
										icon="admin-tools"
										iconPosition="right"
										onClick={() =>
											openConfigModal(addon.slug)
										}
									>
										{__('Configure', '404-to-301')}
									</Button>
								) : (
									<Button
										variant="secondary"
										icon="external"
										iconPosition="right"
										href={addon.link}
										target="_blank"
									>
										{__('View Details', '404-to-301')}
									</Button>
								)}
							</CardFooter>
						</Card>
					))}
				</__experimentalGrid>
			)}
			{isConfigOpen && (
				<Modal
					title={__('Settings', '404-to-301')}
					onRequestClose={() => setConfigOpen(false)}
				>
					{wp.hooks.applyFilters(
						'dd404_addon_modal',
						'',
						configAddon
					)}
					<Button
						variant="primary"
						icon="yes"
						iconPosition="right"
						onClick={() => setConfigOpen(false)}
					>
						{__('Save Changes', '404-to-301')}
					</Button>
				</Modal>
			)}
		</>
	)
}
