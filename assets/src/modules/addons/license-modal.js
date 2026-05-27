import { __, sprintf } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import {
	Button,
	Flex,
	FlexItem,
	Modal,
	Notice,
	TextControl,
} from '@wordpress/components'

/**
 * Per-addon license modal — handles both activation and deactivation
 * for a single addon. Opened from the addon card.
 *
 * @param {Object}   props
 * @param {Object}   props.addon        Addon row.
 * @param {Function} props.onActivate   `(slug, key) => Promise<row|null>`
 * @param {Function} props.onDeactivate `(slug) => Promise<row|null>`
 * @param {Function} props.onClose      Close handler.
 */
const LicenseModal = ({ addon, onActivate, onDeactivate, onClose }) => {
	const [key, setKey] = useState('')
	const [isWorking, setIsWorking] = useState(false)

	const isActive = addon.license_status === 'active'

	const handleActivate = async () => {
		if (!key.trim()) {
			return
		}
		setIsWorking(true)
		const next = await onActivate(addon.slug, key.trim())
		setIsWorking(false)
		if (next) {
			onClose()
		}
	}

	const handleDeactivate = async () => {
		setIsWorking(true)
		const next = await onDeactivate(addon.slug)
		setIsWorking(false)
		if (next) {
			onClose()
		}
	}

	return (
		<Modal
			title={sprintf(
				/* translators: %s: addon name */
				__('Manage license — %s', '404-to-301'),
				addon.title,
			)}
			onRequestClose={onClose}
			className="d404-license-modal"
			size="small"
		>
			{!addon.has_license && (
				<Notice status="warning" isDismissible={false}>
					{__(
						'This add-on does not have licensing configured. It may be free, or its Freemius integration is not yet enabled in this build.',
						'404-to-301',
					)}
				</Notice>
			)}

			{addon.has_license && isActive && (
				<>
					<Notice status="success" isDismissible={false}>
						{sprintf(
							/* translators: %s: masked key */
							__(
								'License is active. Stored key: %s',
								'404-to-301',
							),
							addon.license_masked || '***',
						)}
					</Notice>

					<Flex justify="flex-end" gap={2} style={{ marginTop: '1rem' }}>
						<FlexItem>
							<Button variant="tertiary" onClick={onClose}>
								{__('Close', '404-to-301')}
							</Button>
						</FlexItem>
						<FlexItem>
							<Button
								variant="secondary"
								isDestructive
								isBusy={isWorking}
								disabled={isWorking}
								onClick={handleDeactivate}
							>
								{isWorking
									? __('Deactivating…', '404-to-301')
									: __('Deactivate license', '404-to-301')}
							</Button>
						</FlexItem>
					</Flex>
				</>
			)}

			{addon.has_license && !isActive && (
				<>
					<p>
						{__(
							'Paste the license key issued for this add-on to unlock premium features and automatic updates.',
							'404-to-301',
						)}
					</p>

					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('License key', '404-to-301')}
						value={key}
						onChange={setKey}
						placeholder="xxxx-xxxx-xxxx-xxxx"
						autoFocus
					/>

					<Flex
						justify="flex-end"
						gap={2}
						style={{ marginTop: '1rem' }}
					>
						<FlexItem>
							<Button variant="tertiary" onClick={onClose}>
								{__('Cancel', '404-to-301')}
							</Button>
						</FlexItem>
						<FlexItem>
							<Button
								variant="primary"
								isBusy={isWorking}
								disabled={isWorking || !key.trim()}
								onClick={handleActivate}
							>
								{isWorking
									? __('Activating…', '404-to-301')
									: __('Activate license', '404-to-301')}
							</Button>
						</FlexItem>
					</Flex>
				</>
			)}
		</Modal>
	)
}

export default LicenseModal
