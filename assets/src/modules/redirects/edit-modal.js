import { __ } from '@wordpress/i18n'
import { useState, useEffect, useMemo } from '@wordpress/element'
import {
	Button,
	Flex,
	FlexItem,
	Modal,
	__experimentalVStack as VStack,
} from '@wordpress/components'
import { DataForm, isItemValid } from '@wordpress/dataviews'
import { redirectFormFields, redirectFormLayout } from './form-fields'

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
 * @param {Function}      props.onSave        `(payload) => Promise<boolean>`
 * @param {Function}      props.onClose       Close handler.
 */
const EditRedirect = ({
	redirect = null,
	initialValues = null,
	onSave,
	onClose,
}) => {
	const isEdit = !!redirect
	const [form, setForm] = useState(() =>
		buildSeed(redirect || initialValues || {}),
	)
	const [isWorking, setIsWorking] = useState(false)

	// Re-seed when the edited row changes (the page-level state can
	// swap which row is being edited without remounting the modal).
	useEffect(() => {
		if (redirect) {
			setForm(buildSeed(redirect))
		}
	}, [redirect])

	// `isItemValid` runs every field's `isValid` callback against the
	// current draft. The submit button stays disabled until they all
	// pass — currently just the "source must be non-empty" check.
	const canSubmit = useMemo(
		() => isItemValid(form, redirectFormFields, redirectFormLayout),
		[form],
	)

	// DataForm emits partial edits — merge them into the current
	// form state so unrelated fields aren't clobbered.
	const handleChange = (edits) =>
		setForm((current) => ({ ...current, ...edits }))

	const handleSubmit = async (event) => {
		event.preventDefault()
		if (!canSubmit) {
			return
		}
		setIsWorking(true)
		const ok = await onSave(form)
		setIsWorking(false)
		if (ok) {
			onClose()
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
					<DataForm
						data={form}
						fields={redirectFormFields}
						form={redirectFormLayout}
						onChange={handleChange}
					/>
				</VStack>

				<Flex justify="flex-end" gap={2} style={{ marginTop: '1.5rem' }}>
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
