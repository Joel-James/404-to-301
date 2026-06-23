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
		// Center the short, fixed-size data columns (Hits, Status,
		// Last hit). The width-capped, potentially-long columns —
		// Referrer, IP (IPv6 can be long), User agent — and the primary
		// column (404 Path) stay left-aligned so they truncate cleanly;
		// centering fights truncation. `align` maps to DataViews'
		// `cell-align-center` class and also overrides the integer
		// default right-alignment for Hits / Status.
		styles: {
			ref: { maxWidth: '260px' },
			ip: { maxWidth: '160px' },
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
