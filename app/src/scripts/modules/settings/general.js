import Vue from 'vue'
import './styles/main.scss'
import '@/components/form-submit'

Vue.config.productionTip = false

new Vue({
	el: '#dd404-settings-app',

	data: {
		nonce: '',
		page: '',
		processing: false,
	},

	methods: {
		processForm() {
			this.processing = true
		}
	}
})
