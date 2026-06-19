/**
 * Whether a DataViews `view` has an active search or filter.
 *
 * Used to pick the right empty-state copy: a genuinely empty table
 * ("nothing here yet") reads differently from one the user has filtered
 * down to zero rows ("nothing matches — clear filters").
 *
 * @param {Object} view DataViews view state.
 * @return {boolean} True when a search term or any filter is set.
 */
const isViewFiltered = (view) =>
	Boolean(view?.search) ||
	(Array.isArray(view?.filters) && view.filters.length > 0)

export default isViewFiltered
