import Vue from 'vue'
import Main from './logs/main'
import router from './logs/router'
import { __, sprintf } from '@wordpress/i18n'

Vue.config.productionTip = false;

// Global functions.
Vue.mixin( {
	methods: { __, sprintf }
} );

/* eslint-disable no-new */
new Vue( {
	el: '#dd404-logs-app',
	router,
	render: h => h( Main )
} );
