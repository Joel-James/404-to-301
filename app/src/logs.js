import './styles/logs.scss';
import { __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import { DataViews } from '@wordpress/dataviews/wp';
import { filterSortAndPaginate } from './modules/logs/filter-and-sort-data-view';
import { createRoot, useMemo, useState } from '@wordpress/element';
import { __experimentalHeading as Heading } from '@wordpress/components';

const SettingsTitle = () => {
	return (
		<Heading level={1}>
			{__('404 to 301', 'wp-react')}
		</Heading>
	);
};

const fields = [
	{
		id: 'id',
		label: __('ID', '404-to-301'),
		enableSorting: true,
		getValue: ({ item }) => parseInt(item.id),
	},
	{
		id: 'date',
		label: __('Date & Time', '404-to-301'),
		enableSorting: true,
	},
	{
		id: 'url',
		label: __('404 URL', '404-to-301'),
		enableSorting: false,
		enableGlobalSearch: false,
	},
	{
		id: 'referrer',
		label: __('Referrer', '404-to-301'),
		enableSorting: false,
		enableGlobalSearch: false,
	},
	{
		id: 'ip',
		label: __('IP Address', '404-to-301'),
		enableSorting: false,
		enableGlobalSearch: false,
	},
	{
		id: 'agent',
		label: __('User Agent', '404-to-301'),
		enableSorting: false,
		enableGlobalSearch: false,
	},
	{
		id: 'redirect',
		label: __('Redirect', '404-to-301'),
		enableSorting: false,
		enableGlobalSearch: false,
		render: ({ item }) => (
			<a target="_blank" href={item.redirect} rel="noreferrer">
				{item.redirect}
			</a>
		),
	}
];

const primaryField = 'id';

const defaultLayouts = {
	table: {
		layout: {
			primaryField,
		},
	}
};

const LogsPage = async () => {
	const [isLoading, setIsLoading] = useState(true);
	const [logs, setLogs] = useState([]);
	const [pagination, setPagination] = useState({ totalItems: 0, totalPages: 0 });

	const [ view, setView ] = useState( {
		type: 'table',
		perPage: 10,
		layout: defaultLayouts.table.layout,
		fields: [
			'id',
			'date',
			'url',
			'referrer',
			'ip',
			'agent',
			'redirect'
		],
	} );

	const updateLogs = async () => {
		const { data, paginationInfo } = await filterSortAndPaginate([], view, fields);

		setLogs(data)
		setPagination(paginationInfo)
	}

	setIsLoading(false);

	const actions = [
		{
			id: 'redirect',
			label: __('Custom Redirect'),
			callback: ([item]) => {
				window.alert(`Redirect ${item.id}`)
			},
		},
		{
			id: 'configure',
			label: __('Configure'),
			callback: ([item]) => {
				window.alert(`Configure ${item.id}`)
			},
		},
		{
			id: 'delete',
			label: __('Delete'),
			callback: ([item]) => {
				window.alert(`Deleted ${item.id}`)
			},
		},
	];

	const viewChange = async (view) => {
		setView(view)
		await updateLogs()
	}

	return (
		<>
			<SettingsTitle/>
			<DataViews
				data={logs}
				fields={fields}
				view={view}
				onChangeView={viewChange}
				defaultLayouts={defaultLayouts}
				actions={actions}
				paginationInfo={pagination}
				isLoading={isLoading}
			/>
		</>
	);
};

domReady(() => {
	const root = createRoot(
		document.getElementById('redirectpress-logs-app')
	);

	root.render(<LogsPage/>);
});