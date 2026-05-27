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

const BASE = '/404-to-301/v1/redirects'

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
		view.filters.forEach((filter) => {
			if (
				filter &&
				filter.field &&
				filter.value !== undefined &&
				filter.value !== ''
			) {
				query[filter.field] = filter.value
			}
		})
	}

	return query
}

const useRedirects = (view) => {
	const [items, setItems] = useState([])
	const [total, setTotal] = useState(0)
	const [totalPages, setTotalPages] = useState(1)
	const [isLoading, setIsLoading] = useState(true)
	const requestId = useRef(0)

	const { createSuccessNotice, createErrorNotice } =
		useDispatch(noticesStore)

	const queryKey = useMemo(() => buildQueryKey(view), [view])

	const fetchRedirects = useCallback(async () => {
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
					e?.message ||
						__('Failed to load redirects.', '404-to-301'),
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
		fetchRedirects()
	}, [fetchRedirects])

	const createRedirect = useCallback(
		async (data) => {
			try {
				await apiFetch({
					path: BASE,
					method: 'POST',
					data,
				})
				createSuccessNotice(__('Redirect created.', '404-to-301'))
				await fetchRedirects()
				return true
			} catch (e) {
				createErrorNotice(
					e?.message ||
						__('Failed to create redirect.', '404-to-301'),
				)
				return false
			}
		},
		[fetchRedirects, createSuccessNotice, createErrorNotice],
	)

	const updateRedirect = useCallback(
		async (id, data) => {
			try {
				await apiFetch({
					path: `${BASE}/${id}`,
					method: 'POST',
					data,
				})
				createSuccessNotice(__('Redirect updated.', '404-to-301'))
				await fetchRedirects()
				return true
			} catch (e) {
				createErrorNotice(
					e?.message ||
						__('Failed to update redirect.', '404-to-301'),
				)
				return false
			}
		},
		[fetchRedirects, createSuccessNotice, createErrorNotice],
	)

	const deleteRedirects = useCallback(
		async (ids) => {
			const list = Array.isArray(ids) ? ids : [ids]
			try {
				await Promise.all(
					list.map((id) =>
						apiFetch({
							path: `${BASE}/${id}`,
							method: 'DELETE',
						}),
					),
				)
				createSuccessNotice(
					list.length > 1
						? __('Redirects deleted.', '404-to-301')
						: __('Redirect deleted.', '404-to-301'),
				)
				await fetchRedirects()
				return true
			} catch (e) {
				createErrorNotice(
					e?.message ||
						__('Failed to delete redirect(s).', '404-to-301'),
				)
				return false
			}
		},
		[fetchRedirects, createSuccessNotice, createErrorNotice],
	)

	return {
		items,
		total,
		totalPages,
		isLoading,
		createRedirect,
		updateRedirect,
		deleteRedirects,
		refresh: fetchRedirects,
	}
}

export default useRedirects
