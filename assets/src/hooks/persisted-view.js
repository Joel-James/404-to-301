import { useEffect, useRef, useState } from '@wordpress/element'

/**
 * Subset of the DataViews `view` object we want to persist across reloads.
 *
 * Transient state — `page`, `search` — is intentionally NOT persisted.
 *
 * @type {string[]}
 */
const PERSISTED_KEYS = [
	'type',
	'perPage',
	'fields',
	'filters',
	'layout',
	'sort',
	'titleField',
	'descriptionField',
]

const readStored = (key) => {
	try {
		const raw = window.localStorage.getItem(key)
		if (!raw) {
			return null
		}
		const parsed = JSON.parse(raw)
		return parsed && typeof parsed === 'object' ? parsed : null
	} catch (e) {
		return null
	}
}

const writeStored = (key, view) => {
	try {
		const snapshot = {}
		for (const k of PERSISTED_KEYS) {
			if (view[k] !== undefined) {
				snapshot[k] = view[k]
			}
		}
		window.localStorage.setItem(key, JSON.stringify(snapshot))
	} catch (e) {
		// localStorage may be full, blocked by privacy mode, etc.
	}
}

/**
 * useState-shaped hook that backs a DataViews view by localStorage.
 *
 * On mount, the stored snapshot (if any) is merged on top of the
 * defaults so new fields introduced in code don't get masked by older
 * stored values. On every change, the persistable subset (see
 * `PERSISTED_KEYS`) is written back.
 *
 * @param {string} storageKey  Unique localStorage key.
 * @param {Object} defaultView Default DataViews view config.
 * @return {[Object, Function]} [view, setView]
 */
const usePersistedView = (storageKey, defaultView) => {
	const [view, setView] = useState(() => {
		const stored = readStored(storageKey)
		if (!stored) {
			return defaultView
		}
		return {
			...defaultView,
			...stored,
			layout: {
				...(defaultView.layout || {}),
				...(stored.layout || {}),
				// Deep-merge per-column styles so defaults introduced in
				// code (e.g. column alignment) reach users with an older
				// persisted view, instead of being masked wholesale by
				// the stored `layout`. Stored entries still win per
				// column, so a width a user set by hand is preserved.
				styles: {
					...(defaultView.layout?.styles || {}),
					...(stored.layout?.styles || {}),
				},
			},
		}
	})

	// Track which view we wrote last so we skip the initial-mount write.
	const lastWritten = useRef(null)

	useEffect(() => {
		if (lastWritten.current === null) {
			lastWritten.current = view
			return
		}
		writeStored(storageKey, view)
		lastWritten.current = view
	}, [storageKey, view])

	return [view, setView]
}

/**
 * Build a stable string key from the parts of a view object that
 * actually affect the REST query. Use as an effect dependency so
 * display-only changes (column visibility, density) don't refetch.
 *
 * @param {Object} view DataViews view state.
 * @return {string} Stable JSON key.
 */
export const buildQueryKey = (view) =>
	JSON.stringify({
		page: view.page || 1,
		perPage: view.perPage || 25,
		search: view.search || '',
		sort: view.sort
			? { field: view.sort.field, direction: view.sort.direction }
			: null,
		filters: view.filters || [],
	})

export default usePersistedView
