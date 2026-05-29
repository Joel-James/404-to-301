import { __ } from '@wordpress/i18n'
import { dispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'

const BASE = '/404-to-301/v1/addons'

// --------------------------------------------------------------------- //
// Plain action creators — pure value emitters consumed by the reducer.
// --------------------------------------------------------------------- //

/**
 * Replace the catalog with the given array.
 *
 * @param {Object[]} items Catalog rows.
 * @return {Object} Action.
 */
export const setItems = (items) => ({ type: 'SET_ITEMS', items })

/**
 * Patch a single addon row in-place. Used after license actions so
 * only the affected card flips state, leaving the rest of the
 * catalog cached.
 *
 * @param {Object} addon Shaped addon row.
 * @return {Object} Action.
 */
export const replaceItem = (addon) => ({ type: 'REPLACE_ITEM', addon })

/**
 * Toggle the "initial fetch is in-flight" flag.
 *
 * @param {boolean} value New value.
 * @return {Object} Action.
 */
export const setLoading = (value) => ({
	type: 'SET_LOADING',
	isLoading: !!value,
})

/**
 * Toggle the "user-triggered refresh is in-flight" flag.
 *
 * @param {boolean} value New value.
 * @return {Object} Action.
 */
export const setRefreshing = (value) => ({
	type: 'SET_REFRESHING',
	isRefreshing: !!value,
})

// --------------------------------------------------------------------- //
// Thunk-style generators — yield a control to fetch + dispatch.
// --------------------------------------------------------------------- //

/**
 * Force the server-side Freemius cache to rebuild from the API and
 * replace the in-memory catalog with the freshly-pulled list.
 *
 * Yielded as a generator so `@wordpress/data`'s middleware can run
 * the `API_FETCH` control (the only async step) and feed the result
 * back through the generator. The user-facing notice is dispatched
 * via the core `notices` store regardless of caller — keeps a single
 * source of truth for success messages.
 */
export function* refresh() {
	yield setRefreshing(true)

	try {
		const data = yield {
			type: 'API_FETCH',
			request: { path: `${BASE}/refresh`, method: 'POST' },
		}
		yield setItems(Array.isArray(data?.items) ? data.items : [])
		dispatch(noticesStore).createSuccessNotice(
			__('Add-on list refreshed.', '404-to-301'),
		)
	} catch (err) {
		dispatch(noticesStore).createErrorNotice(
			err?.message ||
				__('Could not refresh the add-on list.', '404-to-301'),
		)
	} finally {
		yield setRefreshing(false)
	}
}

/**
 * Activate a license key for a specific addon.
 *
 * Returns `{ success: boolean, error?: string }` so the license
 * modal can paint the failure reason inline. On success we patch
 * the matching row in-place via `REPLACE_ITEM` and fire a notice.
 *
 * @param {number} id  Freemius project id.
 * @param {string} key License key.
 * @return {Object} Result tuple.
 */
export function* activateLicense(id, key) {
	try {
		const data = yield {
			type: 'API_FETCH',
			request: {
				path: `${BASE}/${encodeURIComponent(id)}/license`,
				method: 'POST',
				data: { key },
			},
		}

		if (data?.success) {
			yield replaceItem(data.addon)
			dispatch(noticesStore).createSuccessNotice(
				__('License activated.', '404-to-301'),
			)
			return { success: true }
		}

		return {
			success: false,
			error: __(
				'License activation failed. Please double-check the key and try again.',
				'404-to-301',
			),
		}
	} catch (err) {
		return {
			success: false,
			error:
				err?.message ||
				__(
					'License activation failed. Please try again.',
					'404-to-301',
				),
		}
	}
}

/**
 * Deactivate the active license for a specific addon.
 *
 * @param {number} id Freemius project id.
 * @return {Object} Result tuple.
 */
export function* deactivateLicense(id) {
	try {
		const data = yield {
			type: 'API_FETCH',
			request: {
				path: `${BASE}/${encodeURIComponent(id)}/license`,
				method: 'DELETE',
			},
		}

		if (data?.success) {
			yield replaceItem(data.addon)
			dispatch(noticesStore).createSuccessNotice(
				__('License deactivated.', '404-to-301'),
			)
			return { success: true }
		}

		return {
			success: false,
			error: __(
				'License deactivation failed. Please try again.',
				'404-to-301',
			),
		}
	} catch (err) {
		return {
			success: false,
			error:
				err?.message ||
				__(
					'License deactivation failed. Please try again.',
					'404-to-301',
				),
		}
	}
}
