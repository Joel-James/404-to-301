import { __ } from '@wordpress/i18n'
import { dateI18n } from '@wordpress/date'
import { ExternalLink } from '@wordpress/components'
import { Truncate } from '../../common'

const statusElements = [
	{ value: 0, label: __('Open', '404-to-301') },
	{ value: 1, label: __('Ignored', '404-to-301') },
	{ value: 2, label: __('Fixed', '404-to-301') },
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
		render: ({ item }) =>
			item.ip ? <Truncate value={item.ip} /> : empty,
	},
	{
		id: 'ua',
		label: __('User Agent', '404-to-301'),
		type: 'text',
		enableGlobalSearch: true,
		enableSorting: false,
		render: ({ item }) =>
			item.ua ? <Truncate value={item.ua} /> : empty,
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
			const slug =
				item.status === 0
					? 'open'
					: item.status === 1
						? 'ignored'
						: 'fixed'
			return (
				<span className={`d404-status d404-status-${slug}`}>
					{label}
				</span>
			)
		},
	},
	{
		id: 'created_at',
		label: __('First seen', '404-to-301'),
		type: 'datetime',
		enableSorting: true,
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
