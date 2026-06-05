/**
 * Reducer for the `d404/addons` store.
 *
 * State shape:
 *
 *     {
 *         items: Object[],           // full catalog
 *         isLoading: boolean,        // initial fetch in flight
 *         isRefreshing: boolean,     // user-triggered refresh in flight
 *     }
 *
 * Stay pure here — every action is a value-only patch. The async
 * work (apiFetch calls) happens in the action generators and the
 * controls.
 */
const DEFAULT_STATE = {
	items: [],
	isLoading: false,
	isRefreshing: false,
}

const reducer = (state = DEFAULT_STATE, action) => {
	switch (action.type) {
		case 'SET_ITEMS':
			return { ...state, items: action.items }

		case 'REPLACE_ITEM': {
			// Patch a single row in place — used after a successful
			// license activate / deactivate so the badge + button
			// state flips without us re-fetching the whole catalog.
			if (!action.addon || !action.addon.id) {
				return state
			}
			return {
				...state,
				items: state.items.map((addon) =>
					Number(addon.id) === Number(action.addon.id)
						? { ...addon, ...action.addon }
						: addon,
				),
			}
		}

		case 'SET_LOADING':
			return { ...state, isLoading: action.isLoading }

		case 'SET_REFRESHING':
			return { ...state, isRefreshing: action.isRefreshing }

		default:
			return state
	}
}

export default reducer
