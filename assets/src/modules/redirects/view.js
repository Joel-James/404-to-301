export const defaultView = {
	type: 'table',
	page: 1,
	perPage: 25,
	search: '',
	titleField: 'source',
	descriptionField: 'target_url',
	fields: ['target_url', 'redirect_type', 'match_type', 'is_active', 'hits'],
	filters: [],
	layout: {
		density: 'comfortable',
		// Center every data column except the long-text Destination
		// (target_url) and the primary column (Source URL), which read
		// better left-aligned. `align` here maps to DataViews'
		// `cell-align-center` class; an explicit value also overrides
		// the integer default right-alignment for Type / Hits. Hidden
		// columns (Destination type, Last hit, Created, Last edited by)
		// are included so they center too once toggled on.
		styles: {
			target_url: { maxWidth: '300px' },
			redirect_type: { align: 'center' },
			match_type: { align: 'center' },
			is_active: { align: 'center' },
			hits: { align: 'center' },
			target_type: { align: 'center' },
			last_hit_at: { align: 'center' },
			created_at: { align: 'center' },
			modified_by: { align: 'center' },
		},
	},
	sort: {
		field: 'updated_at',
		direction: 'desc',
	},
}
