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
		styles: {
			source: { maxWidth: '300px' },
			target_url: { maxWidth: '300px' },
		},
	},
	sort: {
		field: 'updated_at',
		direction: 'desc',
	},
}
