/* global dd404 */
import Vue from 'vue'

// Required components.
import FormSubmit from '@/components/form-submit'
import FormRadioSelect from '@/components/form/radio-select'

const { __, _x, _n, _nx } = wp.i18n;
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

	components: {
		FormSubmit,
		FormRadioSelect
	},

	data: {
		nonce: '',
		page: '',
		type: 301,
		target: 'link',
		items: {
			'301': __( '301 desc', '404-to-301' ),
			'404': __( '404 desc', '404-to-301' ),
			'302': __( '302 desc', '404-to-301' ),
			'307': __( '307 desc', '404-to-301' ),
		}
	},

	computed: {
		showType() {
			return 'page' === this.target || 'link' === this.target
		}
	},

	methods: {
		processForm() {
			this.processing = true
		}
	}
})
