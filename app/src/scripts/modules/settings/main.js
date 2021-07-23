import {createApp} from 'vue'
import FormSubmit from '@/components/form-submit'
import RepeatTable from '@/modules/settings/components/table/repeat-table'

const App = {
	data() {
		return {
			logs: false,
			email: false,
			redirect: true,
			target: 'page',
			loading: false,
		}
	},

	computed: {
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

const Settings = createApp(App);
Settings.component('form-submit', FormSubmit)
Settings.component('repeat-table', RepeatTable)
Settings.mount('#dd404-settings-app')
