/* global wp */
import {createApp} from 'vue'
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
			logs: false,
			email: false,
			redirect: true,
			target: 'page',
			loading: false,
		}
	},
});

// Mixins for global functions.
Settings.mixin({
	methods: {
		__,
	}
})

// Set components.
Settings.component('form-submit', FormSubmit)
Settings.component('repeat-table', RepeatTable)
// Finally mount to DOM.
Settings.mount('#dd404-settings-app')
