import Vue from 'vue'
import LogsApp from './LogsApp.vue'
import router from './router'

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue( {
	el: '#dd404-logs-app',
	router,
	render: h => h( LogsApp )
} );
