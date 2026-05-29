import { createReduxStore, register } from '@wordpress/data'
import reducer from './reducer'
import controls from './controls'
import * as actions from './actions'
import * as selectors from './selectors'
import * as resolvers from './resolvers'

/**
 * Store key for the addons store.
 *
 * Exported so component code can reference it directly instead of
 * stringly-typed `useSelect( (s) => s('d404/addons')... )` — keeps
 * the reference greppable when the store key ever changes.
 */
export const STORE_KEY = 'd404/addons'

/**
 * Build and register the `d404/addons` store.
 *
 * Imported from the entry files (`addons.js` etc) once so the store
 * is available before any component renders. Calling `register`
 * twice is a no-op in `@wordpress/data`, so importing this from
 * multiple entries is safe.
 */
export const store = createReduxStore(STORE_KEY, {
	reducer,
	controls,
	actions,
	selectors,
	resolvers,
})

register(store)

export default store
