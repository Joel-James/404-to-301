import {createApp} from 'vue'

const App = {
	data() {
		return {
			enable: true,
			target: 'page',
			loading: false,
		}
	},

	computed: {
		isDisabled() {
			return !this.enable
		},

		btnText() {
			return this.loading ? 'Saving..' : 'Save Settings'
		}
	},

	methods: {
		saveSettings() {
			this.loading = true
		}
	}
}

const Redirect = createApp(App);
Redirect.mount('#dd404-settings-app')
