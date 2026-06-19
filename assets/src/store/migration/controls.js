import apiFetch from '@wordpress/api-fetch'

/**
 * Custom controls for the `d404/migration` store. See the matching
 * file in `store/addons/` for the rationale.
 */
const controls = {
	API_FETCH({ request }) {
		return apiFetch(request)
	},
}

export default controls
