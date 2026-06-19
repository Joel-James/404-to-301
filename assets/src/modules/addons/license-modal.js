import { __, sprintf } from '@wordpress/i18n'
import { useEffect, useState } from '@wordpress/element'
import {
	Button,
	Flex,
	FlexItem,
	Modal,
	Notice,
	TextControl,
	__experimentalVStack as VStack,
} from '@wordpress/components'

/**
 * Per-addon license management modal.
 *
 * Replaces the inline license form on the addon card. Triggered by
 * the "Manage license" button on each active addon, the modal hosts
 * a single license input + action button that toggles between
 * Activate and Deactivate based on the current `is_license_active`
 * state. Closing the modal (cancel button or backdrop click) calls
 * `onClose` without sending anything to the server.
 *
 * The component is purposely thin — all server calls go through the
 * `onActivate` / `onDeactivate` props which are bound at the page
 * level by `useAddons`. Result of the call drives whether the modal
 * closes (success) or stays open with an error notice (failure).
 *
 * @param {Object}   props
 * @param {Object}   props.addon         Addon row (decorated with license info).
 * @param {Function} props.onActivate    `(id, key) => Promise<boolean>` — true on success.
 * @param {Function} props.onDeactivate  `(id) => Promise<boolean>` — true on success.
 * @param {Function} props.onClose       Close handler.
 */
const LicenseModal = ({ addon, onActivate, onDeactivate, onClose }) => {
	const [key, setKey] = useState(addon.license_key || '')
	const [isWorking, setIsWorking] = useState(false)
	const [error, setError] = useState('')

	// Keep the local input in sync if the server reports a different
	// stored value while the modal is open (e.g. a parallel update
	// from another tab).
	useEffect(() => {
		setKey(addon.license_key || '')
	}, [addon.license_key])

	/**
	 * Click handler for the primary button. Single button does both
	 * activate and deactivate — read the current `is_license_active`
	 * state from the addon row each time so the label / action
	 * never drifts.
	 *
	 * Failures from the server come through as `{ success: false,
	 * error: '...' }` and get painted inline via the local `error`
	 * state. The hook already swallows the error notice for failed
	 * license operations to avoid double-surfacing.
	 */
	const handleSubmit = async () => {
		if (isWorking) {
			return
		}

		// Guard the empty-key case up front so we don't waste a
		// round-trip when there's nothing to activate.
		if (!addon.is_license_active && !key.trim()) {
			setError(__('Enter a license key first.', '404-to-301'))
			return
		}

		setIsWorking(true)
		setError('')

		const result = addon.is_license_active
			? await onDeactivate(addon.id)
			: await onActivate(addon.id, key.trim())

		setIsWorking(false)

		if (result?.success) {
			onClose()
			return
		}

		setError(
			result?.error ||
				__('Something went wrong. Please try again.', '404-to-301'),
		)
	}

	return (
		<Modal
			title={sprintf(
				/* translators: %s: addon title. */
				__('Manage license — %s', '404-to-301'),
				addon.title || '',
			)}
			onRequestClose={onClose}
			className="d404-license-modal"
			size="medium"
		>
			<VStack spacing={4}>
				{addon.is_license_active ? (
					<Notice status="success" isDismissible={false}>
						{__(
							'A license is currently active for this add-on. Deactivate it to enter a different key.',
							'404-to-301',
						)}
					</Notice>
				) : (
					<p style={{ margin: 0 }}>
						{__(
							'Paste your license key below to activate this add-on.',
							'404-to-301',
						)}
					</p>
				)}

				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					type="password"
					label={__('License key', '404-to-301')}
					value={key}
					onChange={(value) => {
						setKey(value)
						setError('')
					}}
					readOnly={addon.is_license_active}
					placeholder={__('Enter license key', '404-to-301')}
				/>

				{error && (
					<Notice status="error" isDismissible={false}>
						{error}
					</Notice>
				)}

				<Flex justify="flex-end" gap={2}>
					<FlexItem>
						<Button
							variant="tertiary"
							onClick={onClose}
							disabled={isWorking}
						>
							{__('Cancel', '404-to-301')}
						</Button>
					</FlexItem>
					<FlexItem>
						<Button
							variant={
								addon.is_license_active
									? 'secondary'
									: 'primary'
							}
							isDestructive={addon.is_license_active}
							isBusy={isWorking}
							disabled={
								isWorking ||
								(!addon.is_license_active && !key.trim())
							}
							onClick={handleSubmit}
						>
							{addon.is_license_active
								? __('Deactivate License', '404-to-301')
								: __('Activate License', '404-to-301')}
						</Button>
					</FlexItem>
				</Flex>
			</VStack>
		</Modal>
	)
}

export default LicenseModal
