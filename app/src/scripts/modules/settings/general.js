/* global dd4t3 */
import Vue from 'vue'

// Required components.
import FormSubmit from '@/components/form-submit'

const axios = require('axios');
const axiosConfig = {
	baseURL: dd4t3.rest.base,
	headers: {
		'X-WP-Nonce': dd4t3.rest.nonce,
	},
};

// Create new axios instance.
Vue.prototype.$axios = axios.create(axiosConfig)

// Disable logs.
Vue.config.productionTip = false

new Vue({
	el: '#dd4t3-settings-app',

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
