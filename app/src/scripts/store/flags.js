import { createStore } from 'vuex'

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
export const Flags = createStore({
	namespaced: true,

	state () {
		return {
			loading: false
		}
	},

	mutations: {
		startLoading (state) {
			state.loading = true
		},

		stopLoading (state) {
			state.loading = false
		}
	}
})