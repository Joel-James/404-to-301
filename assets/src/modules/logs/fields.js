import { __ } from '@wordpress/i18n'
import { dateI18n } from '@wordpress/date'
import { ExternalLink, Tooltip } from '@wordpress/components'
import { Icon, published, unseen, notFound, shuffle } from '@wordpress/icons'
import { Truncate } from '../../common'

const statusElements = [
	{ value: 0, label: __('Open', '404-to-301') },
	{ value: 1, label: __('Ignored', '404-to-301') },
	{ value: 2, label: __('Fixed', '404-to-301') },
]

const statusMeta = {
	0: { slug: 'open', icon: notFound },
	1: { slug: 'ignored', icon: unseen },
	2: { slug: 'fixed', icon: published },
}

// Preset ranges for the "First seen" filter. DataViews has no date
// operator, so the range is offered as a single-select of relative
// windows — each value is a day count that `useLogs` turns into the
// endpoint's `date_from` (0 = today).
const dateRanges = [
	{ value: '0', label: __('Today', '404-to-301') },
	{ value: '7', label: __('Last 7 days', '404-to-301') },
	{ value: '30', label: __('Last 30 days', '404-to-301') },
	{ value: '90', label: __('Last 90 days', '404-to-301') },
	{ value: '365', label: __('Last 12 months', '404-to-301') },
]

const empty = <span className="d404-empty">{'—'}</span>

/**
 * Column definitions for the Logs DataViews table.
 *
 * Field IDs match the keys on the REST response shape produced by
 * {@see \DuckDev\FourNotFour\Api\Logs::shape()}.
 */
export const fields = [
	{
		id: 'url',
		label: __('404 Path', '404-to-301'),
		type: 'text',
		enableGlobalSearch: true,
		enableSorting: true,
		render: ({ item }) => (
			<Truncate value={item.url} className="d404-log-url" />
		),
	},
	{
		id: 'ref',
		label: __('Referrer', '404-to-301'),
		type: 'text',
		enableGlobalSearch: true,
		enableSorting: true,
		render: ({ item }) => {
			if (!item.ref) {
				return empty
			}
			return (
				<Truncate value={item.ref}>
					<ExternalLink href={item.ref}>{item.ref}</ExternalLink>
				</Truncate>
			)
		},
	},
	{
		id: 'ip',
		label: __('IP Address', '404-to-301'),
		type: 'text',
		enableGlobalSearch: true,
		enableSorting: true,
		render: ({ item }) => (item.ip ? <Truncate value={item.ip} /> : empty),
	},
	{
		id: 'ua',
		label: __('User Agent', '404-to-301'),
		type: 'text',
		enableGlobalSearch: true,
		enableSorting: false,
		render: ({ item }) => (item.ua ? <Truncate value={item.ua} /> : empty),
	},
	{
		id: 'hits',
		label: __('Hits', '404-to-301'),
		type: 'integer',
		enableSorting: true,
		render: ({ item }) => <span className="d404-badge">{item.hits}</span>,
	},
	{
		id: 'status',
		label: __('Status', '404-to-301'),
		type: 'integer',
		elements: statusElements,
		filterBy: { operators: ['is', 'isNot'] },
		render: ({ item }) => {
			const label =
				statusElements.find((el) => el.value === item.status)?.label ||
				item.status_label ||
				''
			const meta = statusMeta[item.status] || statusMeta[0]
			const hasRedirect = item.redirect_id != null && item.redirect_id > 0
			return (
				<span className="d404-status-cell">
					<Tooltip text={label}>
						<span
							className={`d404-status d404-status-icon d404-status-${meta.slug}`}
							aria-label={label}
						>
							<Icon icon={meta.icon} size={20} />
						</span>
					</Tooltip>
					{hasRedirect && (
						<Tooltip
							text={__('Has custom redirect', '404-to-301')}
						>
							<span
								className="d404-status-redirect-badge"
								aria-label={__('Has custom redirect', '404-to-301')}
							>
								<Icon icon={shuffle} size={14} />
							</span>
						</Tooltip>
					)}
				</span>
			)
		},
	},
	{
		id: 'created_at',
		label: __('First seen', '404-to-301'),
		type: 'datetime',
		enableSorting: true,
		// Preset relative-window filter (Today / Last 7 days / …).
		// `useLogs` maps the chosen day count to `date_from`.
		elements: dateRanges,
		filterBy: { operators: ['is'] },
		render: ({ item }) =>
			item.created_at ? (
				<time dateTime={item.created_at}>
					{dateI18n('M j, Y g:i a', item.created_at)}
				</time>
			) : (
				empty
			),
	},
	{
		id: 'updated_at',
		label: __('Last hit', '404-to-301'),
		type: 'datetime',
		enableSorting: true,
		render: ({ item }) =>
			item.updated_at ? (
				<time dateTime={item.updated_at}>
					{dateI18n('M j, Y g:i a', item.updated_at)}
				</time>
			) : (
				empty
			),
	},
]
