import { __ } from '@wordpress/i18n'
import { dispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import { setItems, setLoading } from './actions'

const BASE = '/404-to-301/v1/addons'

/**
 * Resolver for `getItems()`.
 *
 * `@wordpress/data` auto-fires this once when any consumer reads
 * `select('d404/addons').getItems()` and the result is not yet
 * resolved. Subsequent reads return the cached array straight from
 * the reducer — no extra fetches when components mount/unmount on
 * tab switches.
 *
 * That single-fetch behaviour is the whole point of moving addons
 * into the store: switching between Catalog and Support no longer
 * triggers a refetch.
 */
export function* getItems() {
	yield setLoading(true)

	try {
		const data = yield {
			type: 'API_FETCH',
			request: { path: BASE },
		}
		yield setItems(Array.isArray(data?.items) ? data.items : [])
	} catch (err) {
		dispatch(noticesStore).createErrorNotice(
			err?.message || __('Could not load the add-on list.', '404-to-301'),
		)
		yield setItems([])
	} finally {
		yield setLoading(false)
	}
}
