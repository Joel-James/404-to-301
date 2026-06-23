import { __ } from '@wordpress/i18n'
import { useState, useEffect, useMemo } from '@wordpress/element'
import {
	Button,
	Flex,
	FlexItem,
	Modal,
	Notice,
	TextControl,
	__experimentalVStack as VStack,
} from '@wordpress/components'
import { DataForm, isItemValid } from '@wordpress/dataviews'
import { redirectFormFields, redirectFormLayout } from './form-fields'

// Form fields and layout with `source` excluded — used when the source is locked.
const formFieldsNoSource = redirectFormFields.filter((f) => f.id !== 'source')
const formLayoutNoSource = {
	...redirectFormLayout,
	fields: redirectFormLayout.fields.filter((id) => id !== 'source'),
}

/**
 * Hydrate the form state with the row being edited (or `initialValues`
 * when creating). Pulled out so the `useEffect` below shares the exact
 * same defaults as the initial `useState`.
 */
const buildSeed = (seed = {}) => ({
	source: seed.source ?? '',
	match_type: seed.match_type ?? 'exact',
	target_type: seed.target_type ?? 'link',
	target_url: seed.target_url ?? '',
	target_page_id: seed.target_page_id ?? 0,
	redirect_type: seed.redirect_type ?? 301,
	query_handling: seed.query_handling ?? 'ignore',
	is_active: seed.is_active ?? true,
	notes: seed.notes ?? '',
})

/**
 * Create / edit modal for a single redirect.
 *
 * Form internals are delegated to `@wordpress/dataviews`' `DataForm`,
 * which consumes the field descriptors in {@see redirectFormFields}.
 * This component keeps the Modal chrome, submit + cancel buttons, the
 * REST-call dance, and the "edit vs. create" labelling.
 *
 * @param {Object}        props
 * @param {Object|null}   props.redirect      Existing row when editing; null when creating.
 * @param {Object|null}   props.initialValues Optional pre-filled values for the
 *                                            create form (e.g. when the Logs page
 *                                            launches it with a 404 path seeded
 *                                            into `source`). Ignored when
 *                                            `redirect` is set.
 * @param {boolean}       props.lockSource    When true the source field is shown
 *                                            as read-only text and excluded from
 *                                            the editable form. Automatically
 *                                            applied when `redirect.has_linked_log`
 *                                            is true so the field stays locked
 *                                            even when not opened from the Logs page.
 * @param {Function}      props.onSave        `(payload) => Promise<boolean>`
 * @param {Function}      props.onClose       Close handler.
 */
const EditRedirect = ({
	redirect = null,
	initialValues = null,
	lockSource = false,
	onSave,
	onClose,
}) => {
	const isEdit = !!redirect
	const [form, setForm] = useState(() =>
		buildSeed(redirect || initialValues || {}),
	)
	const [isWorking, setIsWorking] = useState(false)
	const [submitError, setSubmitError] = useState(null)

	// Re-seed when the edited row changes (the page-level state can
	// swap which row is being edited without remounting the modal).
	useEffect(() => {
		if (redirect) {
			setForm(buildSeed(redirect))
		}
	}, [redirect])

	// `isItemValid` runs every field's `isValid` callback against the
	// current draft. When the source is locked it's excluded from the
	// editable fields but still present in `form` state, so validation
	// still passes without the user having to touch it.
	const activeFields = lockSource ? formFieldsNoSource : redirectFormFields
	const activeLayout = lockSource ? formLayoutNoSource : redirectFormLayout

	const canSubmit = useMemo(
		() => isItemValid(form, activeFields, activeLayout),
		[form, activeFields, activeLayout],
	)

	// DataForm emits partial edits — merge them into the current
	// form state so unrelated fields aren't clobbered. Clear any
	// stale submit error the moment the user starts fixing things.
	const handleChange = (edits) => {
		setForm((current) => ({ ...current, ...edits }))
		if (submitError) {
			setSubmitError(null)
		}
	}

	const handleSubmit = async (event) => {
		event.preventDefault()
		if (!canSubmit) {
			return
		}
		setIsWorking(true)
		setSubmitError(null)
		const result = await onSave(form)
		setIsWorking(false)

		// Backwards-compat: tolerate hooks that still return a bare
		// boolean, even though the redirects hook now hands back
		// `{ ok, error }` so we can surface the message inline.
		const ok = result === true || result?.ok === true
		if (ok) {
			onClose()
			return
		}
		if (result && result.error) {
			setSubmitError(result.error)
		}
	}

	return (
		<Modal
			title={
				isEdit
					? __('Edit redirect', '404-to-301')
					: __('Add redirect', '404-to-301')
			}
			onRequestClose={onClose}
			size="medium"
		>
			<form onSubmit={handleSubmit}>
				<VStack spacing={4}>
					{submitError && (
						<Notice
							status="error"
							isDismissible
							onRemove={() => setSubmitError(null)}
						>
							{submitError.message}
						</Notice>
					)}
					{lockSource && (
						<div className="d404-source-locked">
							<TextControl
								label={__(
									'Source URL or pattern',
									'404-to-301',
								)}
								value={form.source}
								onChange={() => {}}
								disabled
								__nextHasNoMarginBottom
							/>
							<p className="d404-source-locked__notice">
								{__(
									'Source is locked because this redirect is linked to a 404 log.',
									'404-to-301',
								)}
							</p>
						</div>
					)}
					<DataForm
						data={form}
						fields={activeFields}
						form={activeLayout}
						onChange={handleChange}
					/>
				</VStack>

				<Flex
					justify="flex-end"
					gap={2}
					style={{ marginTop: '1.5rem' }}
				>
					<FlexItem>
						<Button variant="tertiary" onClick={onClose}>
							{__('Cancel', '404-to-301')}
						</Button>
					</FlexItem>
					<FlexItem>
						<Button
							variant="primary"
							type="submit"
							isBusy={isWorking}
							disabled={isWorking || !canSubmit}
						>
							{isEdit
								? __('Save changes', '404-to-301')
								: __('Create redirect', '404-to-301')}
						</Button>
					</FlexItem>
				</Flex>
			</form>
		</Modal>
	)
}

export default EditRedirect
