import { __ } from '@wordpress/i18n'
import { useState, useEffect } from '@wordpress/element'
import {
	Button,
	Flex,
	FlexItem,
	Modal,
	SelectControl,
	TextControl,
	TextareaControl,
	ToggleControl,
	__experimentalVStack as VStack,
} from '@wordpress/components'

/**
 * Create / edit modal for a single redirect.
 *
 * @param {Object}        props
 * @param {Object|null}   props.redirect Existing row when editing; null when creating.
 * @param {Function}      props.onSave   `(payload) => Promise<boolean>`
 * @param {Function}      props.onClose  Close handler.
 */
const EditRedirect = ({ redirect = null, onSave, onClose }) => {
	const isEdit = !!redirect

	const [form, setForm] = useState({
		source: redirect?.source ?? '',
		match_type: redirect?.match_type ?? 'exact',
		target_type: redirect?.target_type ?? 'link',
		target_url: redirect?.target_url ?? '',
		target_page_id: redirect?.target_page_id ?? 0,
		redirect_type: redirect?.redirect_type ?? 301,
		is_active: redirect?.is_active ?? true,
		notes: redirect?.notes ?? '',
	})

	useEffect(() => {
		if (redirect) {
			setForm({
				source: redirect.source ?? '',
				match_type: redirect.match_type ?? 'exact',
				target_type: redirect.target_type ?? 'link',
				target_url: redirect.target_url ?? '',
				target_page_id: redirect.target_page_id ?? 0,
				redirect_type: redirect.redirect_type ?? 301,
				is_active: redirect.is_active ?? true,
				notes: redirect.notes ?? '',
			})
		}
	}, [redirect])

	const update = (key) => (value) =>
		setForm((current) => ({ ...current, [key]: value }))

	const [isWorking, setIsWorking] = useState(false)

	const handleSubmit = async (event) => {
		event.preventDefault()
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
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					required
					label={__('Source URL or pattern', '404-to-301')}
					help={__(
						'For "Exact" use a full path (e.g. /old-page). For "Prefix" use a starting fragment. For "Regex" use a PCRE expression.',
						'404-to-301',
					)}
					value={form.source}
					onChange={update('source')}
				/>

				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Match type', '404-to-301')}
					value={form.match_type}
					options={[
						{ value: 'exact', label: __('Exact', '404-to-301') },
						{ value: 'prefix', label: __('Prefix', '404-to-301') },
						{ value: 'regex', label: __('Regex', '404-to-301') },
					]}
					onChange={update('match_type')}
				/>

				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Target type', '404-to-301')}
					value={form.target_type}
					options={[
						{ value: 'link', label: __('Custom URL', '404-to-301') },
						{
							value: 'page',
							label: __('Existing page', '404-to-301'),
						},
						{ value: 'none', label: __('No redirect', '404-to-301') },
					]}
					onChange={update('target_type')}
				/>

				{form.target_type === 'link' && (
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						type="url"
						label={__('Target URL', '404-to-301')}
						value={form.target_url}
						onChange={update('target_url')}
					/>
				)}

				{form.target_type === 'page' && (
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						type="number"
						min={0}
						label={__('Target page ID', '404-to-301')}
						value={form.target_page_id}
						onChange={(v) =>
							update('target_page_id')(
								Math.max(0, parseInt(v, 10) || 0),
							)
						}
					/>
				)}

				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Redirect status', '404-to-301')}
					value={String(form.redirect_type)}
					options={[
						{ value: '301', label: '301 — Permanent' },
						{ value: '302', label: '302 — Found' },
						{ value: '307', label: '307 — Temporary' },
					]}
					onChange={(v) => update('redirect_type')(parseInt(v, 10))}
				/>

				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Active', '404-to-301')}
					checked={!!form.is_active}
					onChange={update('is_active')}
				/>

				<TextareaControl
					__nextHasNoMarginBottom
					label={__('Notes (optional)', '404-to-301')}
					value={form.notes}
					onChange={update('notes')}
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
							disabled={isWorking}
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
