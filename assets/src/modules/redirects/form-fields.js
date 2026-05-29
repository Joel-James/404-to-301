/**
 * DataForm field definitions for the redirect create / edit form.
 *
 * Kept separate from the table's `fields.js` on purpose: the table is
 * a read-only column list with `render` callbacks; the form is the
 * editable surface with `Edit` controls. Sharing one array would push
 * extra columns ({@link target_type}, {@link notes}, …) into the
 * DataViews column-visibility toggle without any UX win.
 *
 * Field IDs match the REST request shape consumed by
 * {@see \DuckDev\FourNotFour\Api\Redirects::collect_writable()}.
 */
import { __ } from '@wordpress/i18n'
import { EnumSelectEdit, TextareaEdit, ToggleEdit } from '../../common'

/**
 * Accept absolute http(s) URLs or root-relative paths (e.g. `/about`).
 * Relies on `URL` for the absolute case so we get the same parsing the
 * server will use; the relative branch is a quick prefix + whitespace
 * check since `URL` rejects bare paths without a base.
 */
const isValidRedirectTarget = (value) => {
	if (typeof value !== 'string') {
		return false
	}
	const trimmed = value.trim()
	if (trimmed === '') {
		return false
	}
	if (trimmed.startsWith('/')) {
		return !/\s/.test(trimmed)
	}
	try {
		const url = new URL(trimmed)
		return url.protocol === 'http:' || url.protocol === 'https:'
	} catch {
		return false
	}
}

export const redirectFormFields = [
	{
		id: 'source',
		label: __('Source URL or pattern', '404-to-301'),
		type: 'text',
		description: __(
			'For "Exact" use a full path (e.g. /old-page). For "Prefix" use a starting fragment. For "Regex" use a PCRE expression.',
			'404-to-301',
		),
		// Required: reject an empty / whitespace-only source on the
		// client so the submit button stays disabled until the user
		// types something. Server still re-validates.
		isValid: (item) =>
			typeof item.source === 'string' && item.source.trim() !== '',
	},
	{
		id: 'match_type',
		label: __('Match type', '404-to-301'),
		type: 'text',
		Edit: EnumSelectEdit,
		elements: [
			{ value: 'exact', label: __('Exact', '404-to-301') },
			{ value: 'prefix', label: __('Prefix', '404-to-301') },
			{ value: 'regex', label: __('Regex', '404-to-301') },
		],
	},
	{
		id: 'target_type',
		label: __('Target type', '404-to-301'),
		type: 'text',
		Edit: EnumSelectEdit,
		elements: [
			{ value: 'link', label: __('Custom URL', '404-to-301') },
			{ value: 'page', label: __('Existing page', '404-to-301') },
			{ value: 'none', label: __('No redirect', '404-to-301') },
		],
	},
	{
		id: 'target_url',
		label: __('Target URL', '404-to-301'),
		type: 'text',
		// Only relevant when the row points at a literal URL — the
		// page-id input takes its place for `target_type === 'page'`,
		// and nothing renders for `'none'`.
		isVisible: (data) => data.target_type === 'link',
		// Required when visible; must parse as an absolute http(s) URL
		// or a root-relative path. Ignored when the field is hidden so
		// `target_type === 'page' | 'none'` rows can still submit.
		isValid: (item) =>
			item.target_type !== 'link' ||
			isValidRedirectTarget(item.target_url),
	},
	{
		id: 'target_page_id',
		label: __('Target page ID', '404-to-301'),
		type: 'integer',
		isVisible: (data) => data.target_type === 'page',
		isValid: (item) =>
			item.target_type !== 'page' ||
			Number(item.target_page_id) > 0,
	},
	{
		id: 'redirect_type',
		label: __('Redirect status', '404-to-301'),
		type: 'integer',
		Edit: EnumSelectEdit,
		elements: [
			{ value: 301, label: '301 — Permanent' },
			{ value: 302, label: '302 — Found' },
			{ value: 307, label: '307 — Temporary' },
		],
	},
	{
		id: 'is_active',
		label: __('Active', '404-to-301'),
		// `boolean` isn't a built-in DataForm type; the field's `Edit`
		// callback renders a ToggleControl so the persisted value
		// shape (boolean) survives unchanged.
		type: 'text',
		Edit: ToggleEdit,
	},
	{
		id: 'notes',
		label: __('Notes (optional)', '404-to-301'),
		type: 'text',
		Edit: TextareaEdit,
	},
]

/**
 * DataForm layout descriptor — order + visibility for the redirect
 * form. The `regular` layout stacks fields vertically inside a VStack;
 * `isVisible` on individual fields handles the target-type swap.
 */
export const redirectFormLayout = {
	type: 'regular',
	fields: [
		'source',
		'match_type',
		'target_type',
		'target_url',
		'target_page_id',
		'redirect_type',
		'is_active',
		'notes',
	],
}
