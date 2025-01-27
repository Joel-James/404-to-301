import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * Applies the filtering, sorting and pagination to the raw data based on the view configuration.
 *
 * @param data   Raw data.
 * @param view   View config.
 * @param fields Fields config.
 *
 * @return Filtered, sorted and paginated data.
 */
export async function filterSortAndPaginate(data, view, fields) {
	if (!data) {
		return {
			data: [],
			paginationInfo: { totalItems: 0, totalPages: 0 },
		};
	}

	let filters = {
		search: ''
	};
	let filteredData = [...data];

	// Handle global search.
	if (view.search) {
		filters.search = view.search.trim().toLowerCase();
	}

	// Handle sorting.
	if (view.sort) {
		const fieldId = view.sort.field;

		const fieldToSort = fields.find((field) => {
			return field.id === fieldId;
		});

		if (fieldToSort) {
			filters.orderby = fieldToSort;
			filters.order = view.sort?.direction ?? 'desc';
		}
	}

	// Handle pagination.
	let totalItems = filteredData.length;
	let totalPages = 2;

	if (view.page !== undefined && view.perPage !== undefined) {
		filters.number = view.perPage;
		filters.page = view.page;
	}

	await apiFetch( { path: addQueryArgs( '/404-to-301/v1/logs', filters ) } ).then( ( logs ) => {
		console.log( logs );
		filteredData = logs.data;
	} );

	return {
		data: filteredData,
		paginationInfo: {
			totalItems,
			totalPages,
		},
	};
}
