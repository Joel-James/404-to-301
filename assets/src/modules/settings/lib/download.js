/**
 * Pure browser helpers for the Import / Export panel.
 *
 * Kept separate from the React component so the filename + download
 * mechanics can be unit-tested without rendering, and so the panel
 * itself stays focused on UI + state.
 */

/**
 * Build the filename used for downloaded exports.
 *
 * The site host gives staging vs prod files distinct names so they
 * don't collide in a Downloads folder, and the date suffix makes
 * versions obvious at a glance.
 *
 * @return {string} Filename of the form `404-to-301-settings-<host>-<YYYY-MM-DD>.json`.
 */
export const buildFilename = () => {
	const host = (window.location?.hostname || 'site').replace(/[^a-z0-9.-]/gi, '')
	const date = new Date().toISOString().slice(0, 10)
	return `404-to-301-settings-${host}-${date}.json`
}

/**
 * Trigger a client-side download for a JSON envelope.
 *
 * We can't just point an `<a>` at the REST endpoint — apiFetch handles
 * the `X-WP-Nonce` header for us, and a bare link would be rejected by
 * the permission_callback. So we fetch the body upstream, build a Blob
 * URL here, and synthesise a one-shot anchor click.
 *
 * @param {Object} envelope JSON-serialisable payload to download.
 * @param {string} filename Suggested filename for the browser's save dialog.
 */
export const downloadEnvelope = (envelope, filename) => {
	const blob = new Blob([JSON.stringify(envelope, null, 2)], {
		type: 'application/json',
	})
	const url = URL.createObjectURL(blob)
	const link = document.createElement('a')
	link.href = url
	link.download = filename
	document.body.appendChild(link)
	link.click()
	document.body.removeChild(link)
	URL.revokeObjectURL(url)
}
