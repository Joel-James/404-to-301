/* global dd404 */
import { createStore } from 'vuex'
import {isNetwork} from "../../../../../google-analytics-async/app/src/helpers/utils";

/**
 * Create a new store instance.
 *
 * Centralized management of plugin settings data.
 * We use Vuex to get the settings using API and then
 * store it in a common store. We can access/update the
 * settings from anywhere in the app using available actions.
 * For easier usage, we have some helper functions available
 * in helpers/utils.js
 *
 * @since 4.0
 */
export const store = createStore({
	namespaced: true,

	state () {
		return dd404.settings
	},

	mutations: {
		/**
		 * Update a single value in store.
		 *
		 * This will only update the value in store.
		 * To update in db, you need to call updateValues
		 * mutation.
		 *
		 * @param {object} state Current state.
		 * @param {object} data Data to update.
		 */
		setValue: (state, data) => {
			if (!state.hasOwnProperty(data.module)) {
				return;
			}

			state[data.group][data.key] = data.value
		},
	}
})