import { __ } from '@wordpress/i18n'
import { useDispatch } from '@wordpress/data'
import apiFetch from '@wordpress/api-fetch'
import { store as noticesStore } from '@wordpress/notices'
import EditRedirect from '../redirects/edit-modal'

/**
 * Wraps the Redirects "create" modal for use from the Logs page.
 *
 * Reuses {@see EditRedirect} verbatim — the only differences are:
 *
 *  - the `source` field is pre-filled with the log's URL,
 *  - on save we POST `/redirects`, then PATCH `/logs/{id}` with the
 *    new redirect's `id` so the log row transitions to the
 *    "Custom redirect" status and carries the link forward.
 *
 * EditRedirect already supplies its own <Modal> chrome, so this is
 * mounted *outside* DataViews' `RenderModal` slot (which would wrap
 * it in a second modal). The Logs page mounts it from a controlled
 * `editingLog` state instead.
 *
 * @param {Object}   props
 * @param {Object}   props.log     Log row triggering the action.
 * @param {Function} props.onClose Close handler.
 * @param {Function} props.onSaved Called after a successful save so
 *                                 the Logs table can refetch.
 */
const CustomRedirectModal = ({ log, onClose, onSaved }) => {
	const { createSuccessNotice, createErrorNotice } =
		useDispatch(noticesStore)

	const handleSave = async (payload) => {
		try {
			const created = await apiFetch({
				path: '/404-to-301/v1/redirects',
				method: 'POST',
				data: payload,
			})

			const redirectId = parseInt(created?.id, 10)

			if (redirectId > 0) {
				await apiFetch({
					path: `/404-to-301/v1/logs/${log.id}`,
					method: 'POST',
					data: { redirect_id: redirectId },
				})
			}

			createSuccessNotice(
				__('Custom redirect created.', '404-to-301'),
			)

			if (typeof onSaved === 'function') {
				await onSaved()
			}

			return true
		} catch (e) {
			createErrorNotice(
				e?.message ||
					__('Failed to create custom redirect.', '404-to-301'),
			)
			return false
		}
	}

	// Seed only the source so the rest of the form keeps its
	// EditRedirect defaults (match_type=exact, target_type=link,
	// redirect_type=301, is_active=true). Passed via `initialValues`
	// rather than `redirect` so the modal still renders in "create"
	// mode (Add title, "Create redirect" button).
	return (
		<EditRedirect
			initialValues={{ source: log?.url ?? '' }}
			onClose={onClose}
			onSave={handleSave}
		/>
	)
}

export default CustomRedirectModal
