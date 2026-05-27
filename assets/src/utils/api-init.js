/* global d404 */

import apiFetch from '@wordpress/api-fetch'

/**
 * Configure `@wordpress/api-fetch` so it sends our REST nonce on every
 * request and resolves paths against the WP site root.
 *
 * Called once per entry point, before any data hook runs. Skips
 * silently when the localized config isn't present (eg. unit tests).
 *
 * Adds a request-fingerprint header (`X-D404-Source`) so server logs
 * can tell our calls apart from any other plugin's apiFetch traffic.
 */
const initApi = () => {
	if (typeof d404 === 'undefined') {
		return
	}

	if (d404.restNonce) {
		apiFetch.use(apiFetch.createNonceMiddleware(d404.restNonce))
	}

	// `restUrl` is the namespaced root (`/wp-json/404-to-301/v1/`); the
	// reference hooks pass absolute REST paths like
	// `/404-to-301/v1/logs`, so we resolve those against the site root.
	if (d404.adminUrl) {
		// Drop trailing `wp-admin/` so we end at the site root, then
		// append `/wp-json/` for the rootURL middleware.
		const siteRoot = d404.adminUrl.replace(/wp-admin\/?$/, '')
		apiFetch.use(apiFetch.createRootURLMiddleware(`${siteRoot}wp-json/`))
	}
}

initApi()
