import { createReduxStore, register } from '@wordpress/data'
import reducer from './reducer'
import controls from './controls'
import * as actions from './actions'
import * as selectors from './selectors'
import * as resolvers from './resolvers'

/**
 * Store key for the migration store. See `store/addons/index.js`
 * for the same rationale.
 */
export const STORE_KEY = 'd404/migration'

export const store = createReduxStore(STORE_KEY, {
	reducer,
	controls,
	actions,
	selectors,
	resolvers,
})

register(store)

export default store
