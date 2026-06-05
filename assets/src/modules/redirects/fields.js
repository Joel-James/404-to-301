import { __ } from '@wordpress/i18n'
import { dateI18n } from '@wordpress/date'
import { ExternalLink, Tooltip } from '@wordpress/components'
import { Icon, published, notAllowed } from '@wordpress/icons'
import { Truncate } from '../../common'

export const redirectTypes = [
	{ value: 301, label: __('301 — Moved Permanently', '404-to-301') },
	{ value: 302, label: __('302 — Found', '404-to-301') },
	{ value: 307, label: __('307 — Temporary Redirect', '404-to-301') },
]

export const matchTypes = [
	{ value: 'exact', label: __('Exact', '404-to-301') },
	{ value: 'prefix', label: __('Prefix', '404-to-301') },
	{ value: 'regex', label: __('Regex', '404-to-301') },
]

export const activeStates = [
	{ value: true, label: __('Active', '404-to-301') },
	{ value: false, label: __('Disabled', '404-to-301') },
]

const empty = <span className="d404-empty">{'—'}</span>

const findLabel = (elements, value) =>
	elements.find((el) => String(el.value) === String(value))?.label || value

/**
 * Column definitions for the Redirects DataViews table.
 *
 * Field IDs match the keys on the REST response shape produced by
 * {@see \DuckDev\FourNotFour\Api\Redirects::shape()}.
 */
export const fields = [
	{
		id: 'source',
		label: __('Source URL', '404-to-301'),
		type: 'text',
		enableGlobalSearch: true,
		enableSorting: true,
		render: ({ item }) => (
			<Truncate value={item.source} className="d404-redirect-source" />
		),
	},
	{
		id: 'target_url',
		label: __('Destination', '404-to-301'),
		type: 'text',
		enableGlobalSearch: true,
		enableSorting: true,
		render: ({ item }) => {
			if (item.target_type === 'page' && item.target_page_id) {
				return (
					<span className="d404-redirect-destination">
						{`Page #${item.target_page_id}`}
					</span>
				)
			}
			if (!item.target_url) {
				return empty
			}
			return (
				<Truncate
					value={item.target_url}
					className="d404-redirect-destination"
				>
					<ExternalLink href={item.target_url}>
						{item.target_url}
					</ExternalLink>
				</Truncate>
			)
		},
	},
	{
		id: 'redirect_type',
		label: __('Type', '404-to-301'),
		type: 'integer',
		elements: redirectTypes,
		enableSorting: true,
		filterBy: { operators: ['is', 'isNot'] },
		render: ({ item }) => findLabel(redirectTypes, item.redirect_type),
	},
	{
		id: 'match_type',
		label: __('Match', '404-to-301'),
		type: 'text',
		elements: matchTypes,
		filterBy: { operators: ['is', 'isNot'] },
		render: ({ item }) => findLabel(matchTypes, item.match_type),
	},
	{
		id: 'is_active',
		label: __('Status', '404-to-301'),
		type: 'boolean',
		elements: activeStates,
		filterBy: { operators: ['is'] },
		render: ({ item }) => {
			const label = item.is_active
				? __('Active', '404-to-301')
				: __('Disabled', '404-to-301')
			return (
				<Tooltip text={label}>
					<span
						className={`d404-status d404-status-icon d404-status-${
							item.is_active ? 'active' : 'inactive'
						}`}
						aria-label={label}
					>
						<Icon
							icon={item.is_active ? published : notAllowed}
							size={20}
						/>
					</span>
				</Tooltip>
			)
		},
	},
	{
		id: 'hits',
		label: __('Hits', '404-to-301'),
		type: 'integer',
		enableSorting: true,
		render: ({ item }) => <span className="d404-badge">{item.hits}</span>,
	},
	{
		id: 'last_hit_at',
		label: __('Last hit', '404-to-301'),
		type: 'datetime',
		enableSorting: true,
		render: ({ item }) =>
			item.last_hit_at ? (
				<time dateTime={item.last_hit_at}>
					{dateI18n('M j, Y', item.last_hit_at)}
				</time>
			) : (
				empty
			),
	},
	{
		id: 'created_at',
		label: __('Created', '404-to-301'),
		type: 'datetime',
		enableSorting: true,
		render: ({ item }) =>
			item.created_at ? (
				<time dateTime={item.created_at}>
					{dateI18n('M j, Y', item.created_at)}
				</time>
			) : (
				empty
			),
	},
	{
		// Audit-trail column. Hidden by default — not in
		// `defaultView.fields` — so multi-author teams can opt in from
		// the DataViews "Fields" picker without bloating the table for
		// single-author sites.
		id: 'modified_by',
		label: __('Last edited by', '404-to-301'),
		type: 'text',
		enableSorting: true,
		render: ({ item }) =>
			item.modified_by_name ? (
				<span className="d404-modified-by">{item.modified_by_name}</span>
			) : (
				empty
			),
	},
]
