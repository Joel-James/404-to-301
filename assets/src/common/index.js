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
import Footer from './footer'
import Notices from './notices'
import PageBody from './page-body'
import PageHeader from './page-header'
import TabNav from './tab-nav'
import Truncate from './truncate'

export {
	AppShell,
	BulkActions,
	Footer,
	Notices,
	PageBody,
	PageHeader,
	TabNav,
	Truncate,
}

// DataForm `Edit` components that aren't shipped by `@wordpress/dataviews`.
export { EnumSelectEdit, TextareaEdit, ToggleEdit } from './form-controls'
