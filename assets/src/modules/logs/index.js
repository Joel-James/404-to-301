import { __ } from '@wordpress/i18n'
import List from './list'
import MigrationBanner from '../migration/banner'
import { Notices, PageBody, PageHeader } from '../../common'

/**
 * Logs page — renders the shell (header + body wrapper + notices) and
 * mounts the DataViews-driven {@see List} inside it.
 */
const LogsPage = () => (
	<>
		<PageHeader title={__('404 to 301 - 404 Error Logs', '404-to-301')} />
		<PageBody wide>
			<Notices />
			<MigrationBanner />
			<List />
		</PageBody>
	</>
)

export default LogsPage
