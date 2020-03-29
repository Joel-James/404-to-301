import Vue from 'vue'
import App from './app'

Vue.config.productionTip = false;

// Global vars.
Vue.prototype.$i18n = window.dd4t3i18n;
Vue.prototype.$vars = window.dd4t3Vars;
Vue.prototype.$moduleVars = window.dd4t3ModuleVars;

/* eslint-disable no-new */
new Vue( {
	el: '#dd404-settings-app',
	render: h => h( App )
} );
