import {createApp} from 'vue'

const App = {
	data() {
		return {
			enable: true,
		}
	},

	computed: {
		isDisabled() {
			return !this.enable
		}
	}
}

const Redirect = createApp(App);
Redirect.mount('#dd404-settings-app')
