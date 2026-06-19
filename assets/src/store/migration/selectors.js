/**
 * Selectors for `d404/migration`.
 */

/**
 * Get the last-known migration status snapshot.
 *
 * Triggers the auto-resolver on first read.
 *
 * @param {Object} state Store state.
 * @return {Object|null}
 */
export const getStatus = (state) => state.status

/**
 * Convenience accessor — does the server think the migration is
 * still running?
 *
 * @param {Object} state Store state.
 * @return {boolean}
 */
export const isRunning = (state) => !!state.status?.running

/**
 * Number of rows the server still has to migrate.
 *
 * @param {Object} state Store state.
 * @return {number}
 */
export const getRemaining = (state) => state.status?.remaining || 0

/**
 * Whether the user is in the click-to-start cooldown.
 *
 * @param {Object} state Store state.
 * @return {boolean}
 */
export const isStarting = (state) => state.isStarting
