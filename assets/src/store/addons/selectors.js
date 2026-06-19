/**
 * Selectors for `d404/addons`.
 *
 * Selectors are pure read-only views over state. Every selector that
 * needs the catalog calls {@see getItems()} so the resolver fires
 * once and only once, regardless of how many components subscribe.
 */

/**
 * Get every catalog row.
 *
 * Triggers the auto-resolver on first read; subsequent reads return
 * the cached array without any side effects.
 *
 * @param {Object} state Store state.
 * @return {Object[]}
 */
export const getItems = (state) => state.items

/**
 * Look up a single addon by Freemius project id.
 *
 * @param {Object} state Store state.
 * @param {number} id    Freemius id.
 * @return {Object|undefined}
 */
export const getItemById = (state, id) =>
	state.items.find((addon) => Number(addon.id) === Number(id))

/**
 * Whether the initial catalog fetch is still in flight.
 *
 * @param {Object} state Store state.
 * @return {boolean}
 */
export const isLoading = (state) => state.isLoading

/**
 * Whether a user-triggered refresh is in flight.
 *
 * @param {Object} state Store state.
 * @return {boolean}
 */
export const isRefreshing = (state) => state.isRefreshing
