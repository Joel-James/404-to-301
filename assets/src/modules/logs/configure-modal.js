import { __ } from '@wordpress/i18n'
import { useEffect, useState } from '@wordpress/element'
import {
	Button,
	Flex,
	FlexItem,
	Notice,
	__experimentalVStack as VStack,
} from '@wordpress/components'
import { DataForm } from '@wordpress/dataviews'
import { EnumSelectEdit } from '../../common'

/**
 * The three-option override value-space (Global / Enable / Disable)
 * mirrors the `OVERRIDE_*` constants on the server. Shared across the
 * per-log override selects below.
 */
const overrideOptions = [
	{ value: 0, label: __('Global', '404-to-301') },
	{ value: 1, label: __('Enable', '404-to-301') },
	{ value: 2, label: __('Disable', '404-to-301') },
]

/**
 * DataForm field descriptors for the per-log override toggles.
 *
 * `EnumSelectEdit` is used directly (rather than the built-in `select`)
 * so the SelectControl renders the per-field `description` as help
 * text — DataForm's stock select drops it on the floor.
 *
 * The `override_redirect` field is hidden in the layout when the log
 * is already linked to a custom redirect: that lever is owned by the
 * redirect row's Active toggle, so showing a no-op control here would
 * mislead the admin.
 */
const configureFormFields = [
	{
		id: 'override_redirect',
		label: __('Redirect', '404-to-301'),
		type: 'integer',
		description: __(
			'Override the global redirect setting for this URL only.',
			'404-to-301',
		),
		Edit: EnumSelectEdit,
		elements: overrideOptions,
	},
	{
		id: 'override_email',
		label: __('Email', '404-to-301'),
		type: 'integer',
		description: __(
			'Override whether email alerts fire for this URL.',
			'404-to-301',
		),
		Edit: EnumSelectEdit,
		elements: overrideOptions,
	},
]

/**
 * Resolve the admin URL for the Redirects page so the linked-redirect
 * hint can deep-link to the row's Active toggle.
 */
const redirectsAdminUrl = () => {
	const base = window?.d404?.adminUrl || ''
	const root = base.replace(/wp-admin\/?$/, 'wp-admin/')
	return `${root}admin.php?page=404-to-301-redirects`
}

/**
 * "Configure" modal — per-log overrides for the global Redirect /
 * Email toggles.
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
		override_redirect: Number(log?.override_redirect ?? 0),
		override_email: Number(log?.override_email ?? 0),
	})

	// The selection can swap to a different row while the modal is
	// open (DataViews keeps the same action mounted) — re-seed when
	// the underlying row changes id.
	useEffect(() => {
		if (log) {
			setForm({
				override_redirect: Number(log.override_redirect ?? 0),
				override_email: Number(log.override_email ?? 0),
			})
		}
	}, [log])

	const [isWorking, setIsWorking] = useState(false)

	if (!log) {
		return null
	}

	// When a custom redirect is linked to this log, the per-row redirect
	// always wins at runtime regardless of the override value — showing
	// the toggle would let the admin set it and watch nothing happen.
	// Hide the field and surface the real lever via the hint instead.
	const hasLinkedRedirect = Boolean(log.redirect_id)
	const layout = {
		type: 'regular',
		fields: hasLinkedRedirect
			? ['override_email']
			: ['override_redirect', 'override_email'],
	}

	const handleSubmit = async (event) => {
		event.preventDefault()
		setIsWorking(true)
		const ok = await onSave(log.id, form)
		setIsWorking(false)
		if (ok && typeof closeModal === 'function') {
			closeModal()
		}
	}

	return (
		<form onSubmit={handleSubmit}>
			<p className="d404-modal-subtitle">{log.url}</p>

			{hasLinkedRedirect && (
				<div style={{ marginBottom: '1rem' }}>
					<Notice status="info" isDismissible={false}>
						{__(
							'This log has a linked custom redirect. The Redirect override is managed by the redirect row’s Active toggle.',
							'404-to-301',
						)}{' '}
						<a
							href={redirectsAdminUrl()}
							target="_blank"
							rel="noreferrer"
						>
							{__('Open Custom Redirects', '404-to-301')}
						</a>
					</Notice>
				</div>
			)}

			<VStack spacing={4}>
				<DataForm
					data={form}
					fields={configureFormFields}
					form={layout}
					onChange={(edits) =>
						setForm((current) => ({ ...current, ...edits }))
					}
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
