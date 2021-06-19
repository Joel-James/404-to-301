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
		targets: [
			{
				'key': 'page',
				'label': __( 'Page', '404-to-301' ),
				'desc': __( 'Redirect to an existing page.', '404-to-301' ),
			},
			{
				'key': 'link',
				'label': __( 'Link', '404-to-301' ),
				'desc': __( 'Redirect to a custom link.', '404-to-301' ),
			},
			{
				'key': 'none',
				'label': __( 'None', '404-to-301' ),
				'desc': __( 'No redirect.', '404-to-301' ),
			}
		],
		types: [
			{
				'key': 301,
				'label': __( '301 Redirect', '404-to-301' ),
				'desc': __( 'Redirect to an existing page.', '404-to-301' ),
			},
			{
				'key': 404,
				'label': __( '404 Page', '404-to-301' ),
				'desc': __( 'Redirect to a custom link.', '404-to-301' ),
			},
			{
				'key': 302,
				'label': __( '302 Redirect', '404-to-301' ),
				'desc': __( 'No redirect.', '404-to-301' ),
			},
			{
				'key': 307,
				'label': __( '307 Redirect', '404-to-301' ),
				'desc': __( 'No redirect.', '404-to-301' ),
			}
		],
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
