/**
 * Default DataViews state for the Logs page.
 *
 * The user's adjustments (column visibility, sort direction, density…)
 * are persisted via {@see usePersistedView}; the keys not listed in
 * `PERSISTED_KEYS` (page, search) reset on every page load.
 */
export const defaultView = {
	type: 'table',
	page: 1,
	perPage: 25,
	search: '',
	titleField: 'url',
	descriptionField: 'updated_at',
	fields: ['ref', 'ip', 'hits', 'status', 'updated_at'],
	filters: [],
	layout: {
		density: 'comfortable',
		styles: {
			url: { maxWidth: '320px' },
			ref: { maxWidth: '260px' },
			ua: { maxWidth: '260px' },
		},
	},
	sort: {
		field: 'updated_at',
		direction: 'desc',
	},
}
