import Vue from 'vue'
import Main from './settings/main'
import router from './settings/router'

Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue( {
	el: '#dd404-settings-app',
	router,
	render: h => h( Main )
} );
