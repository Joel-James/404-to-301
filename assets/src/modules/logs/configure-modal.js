import { __ } from '@wordpress/i18n'
import { useEffect, useState } from '@wordpress/element'
import {
	Button,
	Flex,
	FlexItem,
	SelectControl,
	__experimentalVStack as VStack,
} from '@wordpress/components'

/**
 * Three-option select used for each toggle in the modal.
 *
 * 0 = use the global plugin setting,
 * 1 = force enable for this row,
 * 2 = force disable for this row.
 *
 * Mirrors the legacy plugin's per-log "Default / Enable / Disable"
 * affordance and matches the OVERRIDE_* enum on the server.
 */
const overrideOptions = [
	{ value: '0', label: __('Global', '404-to-301') },
	{ value: '1', label: __('Enable', '404-to-301') },
	{ value: '2', label: __('Disable', '404-to-301') },
]

/**
 * "Configure" modal — per-log overrides for the global Redirect /
 * Log / Email toggles.
 *
 * DataViews wraps the rendered tree in its own <Modal>, so this
 * component returns the contents directly (no nested Modal). The
 * outer title is set via `modalHeader` on the action.
 *
 * @param {Object}   props
 * @param {Object[]} props.items      DataViews selection — always
 *                                    length 1 because the action sets
 *                                    `supportsBulk: false`.
 * @param {Function} props.closeModal Provided by DataViews.
 * @param {Function} props.onSave     `(id, payload) => Promise<boolean>`
 */
const ConfigureLog = ({ items, closeModal, onSave }) => {
	const log = items?.[0]

	const [form, setForm] = useState({
		override_redirect: String(log?.override_redirect ?? 0),
		override_log: String(log?.override_log ?? 0),
		override_email: String(log?.override_email ?? 0),
	})

	useEffect(() => {
		if (log) {
			setForm({
				override_redirect: String(log.override_redirect ?? 0),
				override_log: String(log.override_log ?? 0),
				override_email: String(log.override_email ?? 0),
			})
		}
	}, [log])

	const [isWorking, setIsWorking] = useState(false)

	if (!log) {
		return null
	}

	const update = (key) => (value) =>
		setForm((current) => ({ ...current, [key]: value }))

	const handleSubmit = async (event) => {
		event.preventDefault()
		setIsWorking(true)
		const ok = await onSave(log.id, {
			override_redirect: parseInt(form.override_redirect, 10) || 0,
			override_log: parseInt(form.override_log, 10) || 0,
			override_email: parseInt(form.override_email, 10) || 0,
		})
		setIsWorking(false)
		if (ok && typeof closeModal === 'function') {
			closeModal()
		}
	}

	return (
		<form onSubmit={handleSubmit}>
			<p className="d404-modal-subtitle">{log.url}</p>
			<VStack spacing={4}>
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Redirect', '404-to-301')}
					help={__(
						'Override the global redirect setting for this URL only.',
						'404-to-301',
					)}
					value={form.override_redirect}
					options={overrideOptions}
					onChange={update('override_redirect')}
				/>

				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Log', '404-to-301')}
					help={__(
						'Override whether further hits to this URL are logged.',
						'404-to-301',
					)}
					value={form.override_log}
					options={overrideOptions}
					onChange={update('override_log')}
				/>

				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Email', '404-to-301')}
					help={__(
						'Override whether email alerts fire for this URL.',
						'404-to-301',
					)}
					value={form.override_email}
					options={overrideOptions}
					onChange={update('override_email')}
				/>
			</VStack>

			<Flex justify="flex-end" gap={2} style={{ marginTop: '1.5rem' }}>
				<FlexItem>
					<Button variant="tertiary" onClick={closeModal}>
						{__('Cancel', '404-to-301')}
					</Button>
				</FlexItem>
				<FlexItem>
					<Button
						variant="primary"
						type="submit"
						isBusy={isWorking}
						disabled={isWorking}
					>
						{__('Save', '404-to-301')}
					</Button>
				</FlexItem>
			</Flex>
		</form>
	)
}

export default ConfigureLog
