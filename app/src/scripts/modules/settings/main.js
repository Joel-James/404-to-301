/* global wp */
import {createApp} from 'vue'
import App from './app'
import Router from './router'
// Import store.
import {Flags} from '@/store/flags'

require('@/../styles/settings.scss')

// WP's function.
const {__, sprintf} = wp.i18n;

/**
 * Create settings app.
 *
 * @since 4.0.0
 */
const Settings = createApp(App);

// Mixins for global functions.
Settings.mixin({
	methods: {
		__() {
			return __()
		},
		sprintf,
	}
})
// Use vuex.
Settings.use(Flags)
Settings.use(Router)
Settings.config.globalProperties.$vars = window.dd4t3
// Finally mount to DOM.
Settings.mount('#dd4t3-settings-app')
