/* global d404 */

/**
 * Tiny `fetch` wrapper that prefixes requests with the plugin REST
 * namespace and injects the WP REST nonce.
 *
 * Used by the hooks that talk to our custom endpoints
 * (`use-redirects`, `use-logs`, `use-addons`, `use-migration`).
 */
const apiFetch = async (path, options = {}) => {
	const base = (d404 && d404.restUrl) || ''
	const nonce = (d404 && d404.restNonce) || ''
	const url = base + path.replace(/^\//, '')

	const res = await fetch(url, {
		credentials: 'same-origin',
		...options,
		headers: {
			Accept: 'application/json',
			'Content-Type': 'application/json',
			'X-WP-Nonce': nonce,
			...(options.headers || {}),
		},
	})

	if (!res.ok) {
		let message = res.statusText
		try {
			const body = await res.json()
			if (body && body.message) {
				message = body.message
			}
		} catch (_) {
			// JSON parse failed; keep the statusText.
		}

		const err = new Error(message)
		err.status = res.status
		throw err
	}

	// 204 No Content.
	if (res.status === 204) {
		return null
	}

	return res.json()
}

export default apiFetch
