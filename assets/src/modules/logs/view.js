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
		// Center every data column except the long-text ones (Referrer,
		// User agent) and the primary column (404 Path), which read
		// better left-aligned. `align` here maps to DataViews'
		// `cell-align-center` class; an explicit value also overrides
		// the integer default right-alignment for Hits / Status.
		styles: {
			ref: { maxWidth: '260px' },
			ip: { maxWidth: '160px', align: 'center' },
			ua: { maxWidth: '260px' },
			hits: { align: 'center' },
			status: { align: 'center' },
			updated_at: { align: 'center' },
		},
	},
	sort: {
		field: 'updated_at',
		direction: 'desc',
	},
}
