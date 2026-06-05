import { __ } from '@wordpress/i18n'
import { dispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'

const BASE = '/404-to-301/v1/migration'

// --------------------------------------------------------------------- //
// Plain action creators.
// --------------------------------------------------------------------- //

/**
 * Replace the cached status snapshot.
 *
 * @param {Object|null} status Server response, or null when reset.
 * @return {Object} Action.
 */
export const setStatus = (status) => ({ type: 'SET_STATUS', status })

/**
 * Toggle the "user just clicked Start" busy flag.
 *
 * @param {boolean} value New value.
 * @return {Object} Action.
 */
export const setStarting = (value) => ({
	type: 'SET_STARTING',
	isStarting: !!value,
})

// --------------------------------------------------------------------- //
// Thunk-style generators.
// --------------------------------------------------------------------- //

/**
 * Hit `POST /migration` (the start endpoint).
 *
 * Returns the fresh status snapshot so the caller (the migration
 * banner) can decide whether to launch its poll loop. Failures are
 * surfaced via the global notices store and return `null`.
 */
export function* start() {
	yield setStarting(true)

	try {
		const next = yield {
			type: 'API_FETCH',
			request: { path: BASE, method: 'POST' },
		}
		yield setStatus(next)
		yield setStarting(false)
		return next
	} catch (err) {
		dispatch(noticesStore).createErrorNotice(
			err?.message ||
				__('Could not start the migration.', '404-to-301'),
		)
		yield setStarting(false)
		return null
	}
}

/**
 * Hit `POST /migration/tick` to process one chunk.
 *
 * Returns the fresh status so the React-level poll loop can decide
 * whether to continue. Errors are surfaced and `null` is returned
 * so the loop can break cleanly.
 */
export function* tick() {
	try {
		const next = yield {
			type: 'API_FETCH',
			request: { path: `${BASE}/tick`, method: 'POST' },
		}
		yield setStatus(next)
		return next
	} catch (err) {
		dispatch(noticesStore).createErrorNotice(
			err?.message ||
				__(
					'Migration tick failed — will retry on next page load.',
					'404-to-301',
				),
		)
		return null
	}
}

/**
 * Hit `DELETE /migration` (abort).
 */
export function* abort() {
	try {
		const next = yield {
			type: 'API_FETCH',
			request: { path: BASE, method: 'DELETE' },
		}
		yield setStatus(next)
		dispatch(noticesStore).createSuccessNotice(
			__('Migration aborted.', '404-to-301'),
		)
		return next
	} catch (err) {
		dispatch(noticesStore).createErrorNotice(
			err?.message ||
				__('Could not abort the migration.', '404-to-301'),
		)
		return null
	}
}
