/* global wp, dd404 */
import {createApp} from 'vue'
// Import store.
import {Flags} from '@/store/flags'
// Import required components.
import FormSubmit from '@/components/form-submit'
import RepeatTable from '@/modules/settings/components/table/repeat-table'

// WP's function.
const {__} = wp.i18n;

/**
 * Create settings app.
 *
 * @type {App<Element>}
 *
 * @since 4.0.0
 */
const Settings = createApp({
	data() {
		return {
			loading: false,
			target: dd404.settings?.redirect?.target??'link',
			redirect: (dd404.settings?.redirect?.enable??0) > 0,
			logs: (dd404.settings?.logs?.enable??0) > 0,
			email: (dd404.settings?.email?.enable??0) > 0,
		}
	},

	methods: {
		toggleRedirect(ev) {
			this.redirect = ev.target.checked
		},

		toggleLogs(ev) {
			this.logs = ev.target.checked
		},

		toggleEmail(ev) {
			this.email = ev.target.checked
		}
	}
});

// Mixins for global functions.
Settings.mixin({
	methods: {
		__,
	}
})
// Use vuex.
Settings.use(Flags)
// Set components.
Settings.component('form-submit', FormSubmit)
Settings.component('repeat-table', RepeatTable)
// Finally mount to DOM.
Settings.mount('#dd404-settings-app')
