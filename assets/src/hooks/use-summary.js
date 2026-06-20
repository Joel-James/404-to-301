import apiFetch from '@wordpress/api-fetch'
import { useEffect, useState } from '@wordpress/element'

/**
 * Fetch aggregate summary counts from a REST endpoint on mount.
 *
 * @param {string} path REST path, e.g. '/404-to-301/v1/logs/summary'.
 * @return {{ data: Object|null, isLoading: boolean }}
 */
const useSummary = (path) => {
	const [data, setData] = useState(null)
	const [isLoading, setIsLoading] = useState(true)

	useEffect(() => {
		let cancelled = false

		apiFetch({ path })
			.then((res) => {
				if (!cancelled) {
					setData(res)
				}
			})
			.finally(() => {
				if (!cancelled) {
					setIsLoading(false)
				}
			})

		return () => {
			cancelled = true
		}
	}, [path])

	return { data, isLoading }
}

export default useSummary
