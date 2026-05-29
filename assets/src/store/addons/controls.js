import apiFetch from '@wordpress/api-fetch'

/**
 * Custom controls for the `d404/addons` store.
 *
 * Action generators `yield { type: 'API_FETCH', request: {...} }` and
 * `@wordpress/data` resolves the value back from this control. Keeps
 * generators pure (no awaiting) and lets us mock the network layer
 * in tests by swapping this object.
 */
const controls = {
	API_FETCH({ request }) {
		return apiFetch(request)
	},
}

export default controls
