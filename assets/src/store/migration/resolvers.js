import { setStatus } from './actions'

const BASE = '/404-to-301/v1/migration'

/**
 * Resolver for `getStatus()`.
 *
 * Auto-fired once when any consumer reads
 * `select('d404/migration').getStatus()`. The store then keeps the
 * status across re-mounts so toggling between Logs and any other
 * page doesn't repeat the request.
 *
 * Failures swallow silently — the migration banner only renders
 * when the status is non-null AND `legacy_present === true`, so a
 * failed initial fetch just keeps the banner hidden until the next
 * refresh.
 */
export function* getStatus() {
	try {
		const next = yield {
			type: 'API_FETCH',
			request: { path: BASE },
		}
		yield setStatus(next)
	} catch (err) {
		// Silent.
	}
}
