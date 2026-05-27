import { __ } from '@wordpress/i18n'
import General from './general'
import Redirects from './redirects'
import Logs from './logs'
import Notifications from './notifications'

/**
 * Tab registry: key => { label, component }.
 *
 * Order here = display order in the nav. Add-ons can register new
 * tabs by extending this object.
 */
const tabs = {
	general: {
		label: __('General', '404-to-301'),
		component: General,
	},
	redirects: {
		label: __('Redirects', '404-to-301'),
		component: Redirects,
	},
	logs: {
		label: __('Logs', '404-to-301'),
		component: Logs,
	},
	notifications: {
		label: __('Notifications', '404-to-301'),
		component: Notifications,
	},
}

export default tabs
