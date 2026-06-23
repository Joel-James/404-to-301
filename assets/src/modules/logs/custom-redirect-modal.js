import { __ } from '@wordpress/i18n'
import { useState, useEffect } from '@wordpress/element'
import { useDispatch } from '@wordpress/data'
import apiFetch from '@wordpress/api-fetch'
import { store as noticesStore } from '@wordpress/notices'
import EditRedirect from '../redirects/edit-modal'

/**
 * Create or edit the custom redirect for a log entry.
 *
 * Reuses {@see EditRedirect} from the Redirects page — the source field
 * is always locked to the log's 404 path since changing it would break
 * the link between the redirect and the log.
 *
 * - No linked redirect: creates a new redirect then links it to the log.
 * - Existing linked redirect: loads and edits it in place.
 *
 * Mounted outside DataViews' RenderModal slot to avoid nesting modals.
 *
 * @param {Object}   props
 * @param {Object}   props.log     Log row triggering the action.
 * @param {Function} props.onClose Close handler.
 * @param {Function} props.onSaved Called after a successful save.
 */
const CustomRedirectModal = ({ log, onClose, onSaved }) => {
	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore)
	const [existingRedirect, setExistingRedirect] = useState(null)
	const [isLoadingRedirect, setIsLoadingRedirect] = useState(false)

	const hasLinkedRedirect = log?.redirect_id > 0

	// Fetch the linked redirect when editing an existing one.
	useEffect(() => {
		if (!hasLinkedRedirect) {
			return
		}
		setIsLoadingRedirect(true)
		apiFetch({ path: `/404-to-301/v1/redirects/${log.redirect_id}` })
			.then((data) => setExistingRedirect(data))
			.catch(() =>
				createErrorNotice(
					__('Failed to load the linked redirect.', '404-to-301'),
				),
			)
			.finally(() => setIsLoadingRedirect(false))
	}, [log?.redirect_id]) // eslint-disable-line react-hooks/exhaustive-deps

	const handleSave = async (payload) => {
		try {
			if (hasLinkedRedirect) {
				// Update the existing linked redirect.
				await apiFetch({
					path: `/404-to-301/v1/redirects/${log.redirect_id}`,
					method: 'POST',
					data: payload,
				})
				createSuccessNotice(
					__('Custom redirect updated.', '404-to-301'),
				)
			} else {
				// Create a new redirect and link it to the log.
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
			}

			if (typeof onSaved === 'function') {
				await onSaved()
			}

			return true
		} catch (e) {
			return {
				ok: false,
				error: {
					message:
						e?.message ||
						__('Failed to save custom redirect.', '404-to-301'),
				},
			}
		}
	}

	// Don't render the modal until we've finished loading the linked redirect.
	if (hasLinkedRedirect && isLoadingRedirect) {
		return null
	}

	return (
		<EditRedirect
			redirect={hasLinkedRedirect ? existingRedirect : null}
			initialValues={
				hasLinkedRedirect ? null : { source: log?.url ?? '' }
			}
			lockSource
			onClose={onClose}
			onSave={handleSave}
		/>
	)
}

export default CustomRedirectModal
