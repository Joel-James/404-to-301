import { __ } from '@wordpress/i18n'
import { addQueryArgs } from '@wordpress/url'
import apiFetch from '@wordpress/api-fetch'
import { useDispatch } from '@wordpress/data'
import {
	useCallback,
	useEffect,
	useMemo,
	useRef,
	useState,
} from '@wordpress/element'
import { store as noticesStore } from '@wordpress/notices'
import { buildQueryKey } from './persisted-view'

const BASE = '/404-to-301/v1/logs'

/**
 * Translate a DataViews `view` object into REST query parameters.
 *
 * `view.filters` is forwarded verbatim as a structured `filters[]`
 * array. The server-side mapper (`Filter_Mapper::for_logs()`) maps
 * each `{ field, operator, value }` entry onto the appropriate
 * BerlinDB clause (`IN`, `NOT IN`, `compare_query`, or a LIKE).
 *
 * @param {Object} view DataViews view state.
 * @return {Object} Query args for the logs endpoint.
 */
const viewToQuery = (view) => {
	const query = {
		page: view.page || 1,
		per_page: view.perPage || 25,
	}

	if (view.search) {
		query.search = view.search
	}

	if (view.sort?.field) {
		query.orderby = view.sort.field
		query.order = view.sort.direction === 'asc' ? 'asc' : 'desc'
	}

	if (Array.isArray(view.filters)) {
		const filters = view.filters
			.filter(
				(f) =>
					f &&
					f.field &&
					f.operator &&
					f.value !== undefined &&
					f.value !== '' &&
					!(Array.isArray(f.value) && f.value.length === 0),
			)
			.map(({ field, operator, value }) => ({ field, operator, value }))
		if (filters.length > 0) {
			query.filters = filters
		}
	}

	return query
}

/**
 * Logs data hook. Drives the DataViews table on the Logs page.
 *
 * Re-fetches only when the query-relevant subset of the view changes
 * (page, perPage, search, sort, filters). Display-only changes don't
 * trigger a network round-trip.
 *
 * @param {Object} view DataViews view state.
 * @return {Object} Logs state and mutators.
 */
const useLogs = (view) => {
	const [items, setItems] = useState([])
	const [total, setTotal] = useState(0)
	const [totalPages, setTotalPages] = useState(1)
	const [isLoading, setIsLoading] = useState(true)
	const requestId = useRef(0)

	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore)

	const queryKey = useMemo(() => buildQueryKey(view), [view])

	const fetchLogs = useCallback(async () => {
		const ticket = ++requestId.current
		setIsLoading(true)
		try {
			const response = await apiFetch({
				path: addQueryArgs(BASE, viewToQuery(view)),
				parse: false,
			})
			if (ticket !== requestId.current) {
				return
			}
			const data = await response.json()
			setItems(Array.isArray(data) ? data : [])
			setTotal(parseInt(response.headers.get('X-WP-Total'), 10) || 0)
			setTotalPages(
				parseInt(response.headers.get('X-WP-TotalPages'), 10) || 1,
			)
		} catch (e) {
			if (ticket === requestId.current) {
				setItems([])
				setTotal(0)
				setTotalPages(1)
				createErrorNotice(
					e?.message || __('Failed to load logs.', '404-to-301'),
				)
			}
		} finally {
			if (ticket === requestId.current) {
				setIsLoading(false)
			}
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [queryKey, createErrorNotice])

	useEffect(() => {
		fetchLogs()
	}, [fetchLogs])

	const updateLog = useCallback(
		async (id, data) => {
			try {
				await apiFetch({
					path: `${BASE}/${id}`,
					method: 'POST',
					data,
				})
				createSuccessNotice(__('Log updated.', '404-to-301'))
				await fetchLogs()
				return true
			} catch (e) {
				createErrorNotice(
					e?.message || __('Failed to update log.', '404-to-301'),
				)
				return false
			}
		},
		[fetchLogs, createSuccessNotice, createErrorNotice],
	)

	/**
	 * Delete one or many logs in a single API call.
	 *
	 * Sends one `DELETE /logs` with the full `ids` array (the bulk
	 * endpoint), then refetches once on success. Replaces the
	 * earlier `Promise.all` of per-row DELETEs which fanned out a
	 * separate request per id and re-rendered the table after each.
	 */
	const deleteLogs = useCallback(
		async (ids) => {
			const list = (Array.isArray(ids) ? ids : [ids])
				.map((id) => parseInt(id, 10))
				.filter((id) => id > 0)

			if (list.length === 0) {
				return false
			}

			try {
				await apiFetch({
					path: BASE,
					method: 'DELETE',
					data: { ids: list },
				})
				createSuccessNotice(
					list.length > 1
						? __('Logs deleted.', '404-to-301')
						: __('Log deleted.', '404-to-301'),
				)
				await fetchLogs()
				return true
			} catch (e) {
				createErrorNotice(
					e?.message || __('Failed to delete log(s).', '404-to-301'),
				)
				return false
			}
		},
		[fetchLogs, createSuccessNotice, createErrorNotice],
	)

	/**
	 * Flip the status on one or many logs in a single API call.
	 *
	 * The Mark Fixed / Mark Ignored / Reopen bulk actions all funnel
	 * through here — the table is refetched once after the server
	 * confirms instead of per-row.
	 */
	const bulkSetStatus = useCallback(
		async (ids, status) => {
			const list = (Array.isArray(ids) ? ids : [ids])
				.map((id) => parseInt(id, 10))
				.filter((id) => id > 0)

			if (list.length === 0) {
				return false
			}

			try {
				await apiFetch({
					path: `${BASE}/bulk-update`,
					method: 'POST',
					data: { ids: list, status },
				})
				createSuccessNotice(
					list.length > 1
						? __('Logs updated.', '404-to-301')
						: __('Log updated.', '404-to-301'),
				)
				await fetchLogs()
				return true
			} catch (e) {
				createErrorNotice(
					e?.message || __('Failed to update log(s).', '404-to-301'),
				)
				return false
			}
		},
		[fetchLogs, createSuccessNotice, createErrorNotice],
	)

	return {
		items,
		total,
		totalPages,
		isLoading,
		updateLog,
		bulkSetStatus,
		deleteLogs,
		refresh: fetchLogs,
	}
}

export default useLogs
