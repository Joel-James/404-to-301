import Vue from 'vue'
import App from './app'
import { sprintf } from 'sprintf-js'

Vue.config.productionTip = false;

// Global vars.
Vue.prototype.$i18n = window.dd4t3i18n;
Vue.prototype.$vars = window.dd4t3Vars;
Vue.prototype.$moduleVars = window.dd4t3ModuleVars;

// Global functions.
Vue.mixin( {
	methods: { sprintf }
} );

new Vue( {
	el: '#dd404-logs-app',
	render: h => h( App )
} );
