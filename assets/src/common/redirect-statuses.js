/* global d404 */

/**
 * The redirect-status catalogue, shared across the React app.
 *
 * PHP is the single source of truth — `Helpers::redirect_statuses()`
 * is localised onto `d404.redirectStatuses` (see the `Admin\Assets`
 * class). Every select that offers a redirect/error code reads from
 * here, so the Redirects form, the Redirects table and the global
 * 404-fallback setting can never drift out of sync with the server's
 * REST `enum` validation.
 *
 * Each catalogue entry is `{ value:int, label:string, terminal:bool }`.
 * `terminal` codes (410/451) don't redirect — they end the request
 * with a status header — so the editor hides the destination fields
 * when one is selected.
 */
import { __ } from '@wordpress/i18n'

/**
 * Fallback catalogue, mirroring `Helpers::redirect_statuses()`. Used
 * only when the localized payload is absent (eg. unit tests or a
 * context where the app mounts without `wp_localize_script`). At
 * runtime in wp-admin, `d404.redirectStatuses` always wins.
 */
const FALLBACK_STATUSES = [
	{
		value: 301,
		label: __('301 — Moved Permanently (SEO)', '404-to-301'),
		terminal: false,
	},
	{ value: 302, label: __('302 — Found', '404-to-301'), terminal: false },
	{ value: 303, label: __('303 — See Other', '404-to-301'), terminal: false },
	{
		value: 307,
		label: __('307 — Temporary Redirect', '404-to-301'),
		terminal: false,
	},
	{
		value: 308,
		label: __('308 — Permanent Redirect', '404-to-301'),
		terminal: false,
	},
	{ value: 410, label: __('410 — Gone', '404-to-301'), terminal: true },
	{
		value: 451,
		label: __('451 — Unavailable for Legal Reasons', '404-to-301'),
		terminal: true,
	},
]

// Prefer the server-provided catalogue; fall back when it's missing.
export const redirectStatuses =
	typeof d404 !== 'undefined' &&
	Array.isArray(d404.redirectStatuses) &&
	d404.redirectStatuses.length > 0
		? d404.redirectStatuses
		: FALLBACK_STATUSES

// `{ value, label }` elements for DataForm enum selects (all codes).
export const redirectTypes = redirectStatuses.map(({ value, label }) => ({
	value,
	label,
}))

// Codes that don't redirect — emit the status header and stop.
export const terminalStatusCodes = redirectStatuses
	.filter((status) => status.terminal)
	.map((status) => status.value)

// Non-terminal options for the global 404-fallback `SelectControl`,
// whose stored value is a string. The fallback always points at a
// destination, so terminal codes are excluded.
export const redirectingTypeOptions = redirectStatuses
	.filter((status) => !status.terminal)
	.map((status) => ({ value: String(status.value), label: status.label }))
