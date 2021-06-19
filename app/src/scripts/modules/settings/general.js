/* global dd404 */
import Vue from 'vue'

// Required components.
import FormSubmit from '@/components/form-submit'

const axios = require('axios');
const axiosConfig = {
	baseURL: dd404.rest.base,
	headers: {
		'X-WP-Nonce': dd404.rest.nonce,
	},
};

// Create new axios instance.
Vue.prototype.$axios = axios.create(axiosConfig)

// Disable logs.
Vue.config.productionTip = false

new Vue({
	el: '#dd404-settings-app',

	components: { FormSubmit },

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
