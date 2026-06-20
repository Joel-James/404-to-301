/**
 * Barrel export for shared admin UI primitives.
 *
 * Lets consumers do:
 *
 *   import { PageHeader, PageBody, Notices } from '../../common'
 *
 * instead of one import per file.
 */
import AppShell from './app-shell'
import BulkActions from './bulk-actions'
import EmptyState from './empty-state'
import Footer from './footer'
import isViewFiltered from './is-view-filtered'
import Notices from './notices'
import PageBody from './page-body'
import PageHeader from './page-header'
import SummaryCards from './summary-cards'
import TabNav from './tab-nav'
import Truncate from './truncate'

export {
	AppShell,
	BulkActions,
	EmptyState,
	Footer,
	isViewFiltered,
	Notices,
	PageBody,
	PageHeader,
	SummaryCards,
	TabNav,
	Truncate,
}

// Searchable "existing page" picker (standalone + DataForm Edit wrapper).
export { default as PageSelect } from './page-select'

// DataForm `Edit` components that aren't shipped by `@wordpress/dataviews`.
export {
	EnumSelectEdit,
	PageSelectEdit,
	TextareaEdit,
	ToggleEdit,
} from './form-controls'

// Shared redirect-status catalogue (PHP-localised, single source of truth).
export {
	redirectStatuses,
	redirectTypes,
	terminalStatusCodes,
	redirectingTypeOptions,
} from './redirect-statuses'
