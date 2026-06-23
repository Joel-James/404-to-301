import { __ } from '@wordpress/i18n'
import List from './list'
import MigrationBanner from '../migration/banner'
import { Notices, PageBody, PageHeader, SummaryCards } from '../../common'
import useSummary from '../../hooks/use-summary'

/**
 * Logs page — renders the shell (header + body wrapper + notices) and
 * mounts the DataViews-driven {@see List} inside it.
 */
const LogsPage = () => {
	const { data, isLoading } = useSummary('/404-to-301/v1/logs/summary')

	const cards = [
		{ label: __('Total', '404-to-301'), value: data?.total ?? 0 },
		{ label: __('Open', '404-to-301'), value: data?.open ?? 0 },
		{ label: __('Fixed', '404-to-301'), value: data?.fixed ?? 0 },
		{ label: __('Ignored', '404-to-301'), value: data?.ignored ?? 0 },
		{
			label: __('Custom redirects', '404-to-301'),
			value: data?.custom ?? 0,
		},
	]

	return (
		<>
			<PageHeader
				title={__('404 to 301 - 404 Error Logs', '404-to-301')}
			/>
			<PageBody wide>
				<Notices />
				<MigrationBanner />
				<SummaryCards cards={cards} isLoading={isLoading} />
				<List />
			</PageBody>
		</>
	)
}

export default LogsPage
