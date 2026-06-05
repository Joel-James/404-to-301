/**
 * Reducer for the `d404/migration` store.
 *
 * State shape:
 *
 *     {
 *         status: Object|null,  // last-known server status snapshot
 *         isStarting: boolean,  // user just clicked "Start migration"
 *     }
 *
 * The polling loop lives at the hook level (because side-effects /
 * setTimeout chains belong in React, not the store) — the store
 * itself just holds the latest snapshot so multiple components can
 * read it without each spawning their own poll.
 */
const DEFAULT_STATE = {
	status: null,
	isStarting: false,
}

const reducer = (state = DEFAULT_STATE, action) => {
	switch (action.type) {
		case 'SET_STATUS':
			return { ...state, status: action.status }

		case 'SET_STARTING':
			return { ...state, isStarting: action.isStarting }

		default:
			return state
	}
}

export default reducer
