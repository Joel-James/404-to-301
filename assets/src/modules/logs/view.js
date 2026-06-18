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
	descriptionField: 'created_at',
	fields: ['ref', 'ip', 'hits', 'status', 'updated_at'],
	filters: [],
	layout: {
		density: 'comfortable',
		styles: {
			ref: { maxWidth: '260px' },
			ip: { maxWidth: '160px' },
			ua: { maxWidth: '260px' },
		},
	},
	sort: {
		field: 'updated_at',
		direction: 'desc',
	},
}
